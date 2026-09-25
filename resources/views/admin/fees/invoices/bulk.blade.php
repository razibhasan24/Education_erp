@extends('admin.app')
@section('page-title', 'বাল্ক ইনভয়েস (পুরো ক্লাস)')

@section('content')
<form action="{{ route('admin.fees.invoices.bulk.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-7">
            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title">শ্রেণি নির্বাচন</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>শ্রেণি *</label>
                        <select name="class_id" class="form-control select2" required onchange="this.form.submit()">
                            <option value="">নির্বাচন</option>
                            @foreach($classes as $c)<option value="{{ $c->id }}" @selected($classId==$c->id)>{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>শাখা</label>
                        <select name="section_id" class="form-control select2">
                            <option value="">সব শাখা</option>
                            @foreach($sections as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label>মাস *</label>
                            <select name="month" class="form-control" required>
                                @foreach($months as $k=>$v)
                                    <option value="{{ str_pad($k,2,'0',STR_PAD_LEFT) }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 form-group"><label>বছর *</label><input type="number" name="year" value="{{ date('Y') }}" class="form-control" required></div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group"><label>ইনভয়েস তারিখ *</label><input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" class="form-control" required></div>
                        <div class="col-6 form-group"><label>ডিউ তারিখ</label><input type="date" name="due_date" class="form-control"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card card-success">
                <div class="card-header"><h3 class="card-title">ফি ক্যাটাগরি</h3></div>
                <div class="card-body">
                    @if($feeStructures->count())
                        @foreach($feeStructures as $fs)
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input"
                                       id="cat_{{ $fs->fee_category_id }}"
                                       name="category_ids[]" value="{{ $fs->fee_category_id }}" checked>
                                <label class="custom-control-label" for="cat_{{ $fs->fee_category_id }}">
                                    <b>{{ $fs->category->name }}</b> — ৳ {{ number_format($fs->amount, 2) }}
                                </label>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">শ্রেণি নির্বাচন করুন। ফি স্ট্রাকচার থেকে আইটেম লোড হবে।</p>
                    @endif
                </div>
                <div class="card-footer">
                    <button class="btn btn-success btn-block"><i class="fas fa-copy"></i> সব শিক্ষার্থীর ইনভয়েস তৈরি</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
