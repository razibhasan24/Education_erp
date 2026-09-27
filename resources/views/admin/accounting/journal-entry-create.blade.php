@extends('admin.app')
@section('page-title', 'নতুন Journal Entry')

@section('content')
<form action="{{ route('admin.accounting.journal-entries.store') }}" method="POST">
    @csrf
    <div class="card card-primary">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>তারিখ *</label>
                    <input type="date" name="entry_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                </div>
                <div class="col-md-4 form-group">
                    <label>Reference</label>
                    <input type="text" name="reference" class="form-control" placeholder="যেমন: INV-0001">
                </div>
                <div class="col-md-12 form-group">
                    <label>Narration *</label>
                    <input type="text" name="narration" class="form-control" required placeholder="লেনদেনের বিবরণ">
                </div>
            </div>
        </div>
    </div>

    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">Journal Lines</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-light" onclick="addLine()">
                    <i class="fas fa-plus"></i> নতুন লাইন
                </button>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-bordered" id="linesTable">
                <thead class="thead-light">
                    <tr>
                        <th width="40%">Account</th>
                        <th width="20%">Debit</th>
                        <th width="20%">Credit</th>
                        <th>Description</th>
                        <th width="40"></th>
                    </tr>
                </thead>
                <tbody id="linesBody">
                    {{-- Lines will be added via JS --}}
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <th class="text-right">মোট</th>
                        <th class="text-right"><span id="totalDebit">0.00</span></th>
                        <th class="text-right"><span id="totalCredit">0.00</span></th>
                        <th colspan="2">
                            <span id="balanceStatus" class="badge badge-secondary">ব্যালেন্স চেক হয়নি</span>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
                <i class="fas fa-save"></i> সংরক্ষণ করুন
            </button>
            <a href="{{ route('admin.accounting.journal-entries') }}" class="btn btn-secondary btn-lg">বাতিল</a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
const accounts = @json($accounts->map(fn($a) => ['id' => $a->id, 'code' => $a->code, 'name' => $a->name]));
let lineIndex = 0;

function accountOptions(selected = '') {
    let opts = '<option value="">-- নির্বাচন --</option>';
    accounts.forEach(a => {
        opts += `<option value="${a.id}" ${selected == a.id ? 'selected' : ''}>${a.code} - ${a.name}</option>`;
    });
    return opts;
}

function addLine() {
    const idx = lineIndex++;
    const html = `
        <tr id="line_${idx}">
            <td>
                <select name="lines[${idx}][account_id]" class="form-control form-control-sm" required>
                    ${accountOptions()}
                </select>
            </td>
            <td><input type="number" step="0.01" min="0" name="lines[${idx}][debit]" value="0" class="form-control form-control-sm debit-input" oninput="recalc()"></td>
            <td><input type="number" step="0.01" min="0" name="lines[${idx}][credit]" value="0" class="form-control form-control-sm credit-input" oninput="recalc()"></td>
            <td><input type="text" name="lines[${idx}][description]" class="form-control form-control-sm"></td>
            <td class="text-center">
                <button type="button" class="btn btn-xs btn-danger" onclick="removeLine(${idx})"><i class="fas fa-times"></i></button>
            </td>
        </tr>
    `;
    document.getElementById('linesBody').insertAdjacentHTML('beforeend', html);
    recalc();
}

function removeLine(idx) {
    document.getElementById('line_' + idx)?.remove();
    recalc();
}

function recalc() {
    let td = 0, tc = 0;
    document.querySelectorAll('.debit-input').forEach(i => td += parseFloat(i.value || 0));
    document.querySelectorAll('.credit-input').forEach(i => tc += parseFloat(i.value || 0));

    document.getElementById('totalDebit').innerText = td.toFixed(2);
    document.getElementById('totalCredit').innerText = tc.toFixed(2);

    const badge = document.getElementById('balanceStatus');
    const btn = document.getElementById('submitBtn');

    if (Math.abs(td - tc) < 0.01 && td > 0) {
        badge.className = 'badge badge-success';
        badge.innerText = '✓ ব্যালেন্সড';
        btn.disabled = false;
    } else {
        badge.className = 'badge badge-danger';
        badge.innerText = '✗ Debit = Credit হতে হবে (পার্থক্য: ' + (td - tc).toFixed(2) + ')';
        btn.disabled = true;
    }
}

// শুরুতে ২টি লাইন
addLine();
addLine();
</script>
@endpush
