<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    private string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    public function index()
    {
        $files = collect(File::files($this->backupDir))
            ->map(fn($f) => [
                'name' => $f->getFilename(),
                'size' => $this->humanSize($f->getSize()),
                'created' => date('d M, Y H:i', $f->getMTime()),
                'path' => $f->getRealPath(),
            ])
            ->sortByDesc('created')
            ->values();

        return view('admin.backup.index', compact('files'));
    }

    public function create()
    {
        $filename = 'backup-' . date('Y-m-d_His') . '.sql';
        $path = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        try {
            $this->dumpDatabase($path);
            return back()->with('success', "ব্যাকআপ তৈরি হয়েছে: {$filename}");
        } catch (\Exception $e) {
            return back()->with('error', 'ব্যাকআপ ব্যর্থ: ' . $e->getMessage());
        }
    }

    /**
     * mysqldump এর মাধ্যমে SQL ফাইল তৈরি
     */
    private function dumpDatabase(string $path): void
    {
        $host = config('database.connections.mysql.host');
        $db = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');
        $port = config('database.connections.mysql.port', 3306);

        $mysqldump = $this->findMysqldump();

        $cmd = sprintf(
            '%s --host=%s --port=%s --user=%s %s %s > %s',
            escapeshellcmd($mysqldump),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            $pass ? '--password=' . escapeshellarg($pass) : '',
            escapeshellarg($db),
            escapeshellarg($path)
        );

        // Windows এ কাজ করার জন্য
        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = sprintf(
                '"%s" --host=%s --port=%s --user=%s %s %s > "%s"',
                $mysqldump,
                $host,
                $port,
                $user,
                $pass ? '--password=' . $pass : '',
                $db,
                $path
            );
        }

        exec($cmd . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0 && !File::exists($path)) {
            throw new \Exception('mysqldump কমান্ড ব্যর্থ। নিশ্চিত করুন MySQL ইনস্টল ও PATH এ আছে।');
        }
    }

    private function findMysqldump(): string
    {
        // Windows ও Linux এর কমন পাথ
        $paths = [
            'mysqldump',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
        ];

        foreach ($paths as $p) {
            if ($p === 'mysqldump' || File::exists($p)) {
                return $p;
            }
        }

        return 'mysqldump';
    }

    public function download(string $filename)
    {
        $path = $this->backupDir . DIRECTORY_SEPARATOR . $filename;
        if (!File::exists($path)) {
            return back()->with('error', 'ফাইল পাওয়া যায়নি।');
        }
        return response()->download($path);
    }

    public function destroy(string $filename)
    {
        $path = $this->backupDir . DIRECTORY_SEPARATOR . $filename;
        if (File::exists($path)) {
            File::delete($path);
            return back()->with('success', 'ব্যাকআপ মুছে ফেলা হয়েছে।');
        }
        return back()->with('error', 'ফাইল পাওয়া যায়নি।');
    }

    /**
     * ফাইল থেকে restore (শুধু SQL কমান্ড চালাবে)
     */
    public function restore(Request $request)
    {
        $request->validate(['filename' => 'required|string']);

        $path = $this->backupDir . DIRECTORY_SEPARATOR . $request->filename;
        if (!File::exists($path)) {
            return back()->with('error', 'ফাইল পাওয়া যায়নি।');
        }

        try {
            $sql = File::get($path);
            DB::unprepared($sql);
            return back()->with('success', 'ডেটাবেস রিস্টোর হয়েছে।');
        } catch (\Exception $e) {
            return back()->with('error', 'রিস্টোর ব্যর্থ: ' . $e->getMessage());
        }
    }

    private function humanSize($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
