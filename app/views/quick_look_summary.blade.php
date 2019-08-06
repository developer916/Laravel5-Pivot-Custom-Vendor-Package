@extends('layout')

@section('content')

    <h1 id="page_title">Quick Look Results</h1>
    <div class="row">
        <div class="chart_header col-md-6 first">5 is the highest score</div>
        <div class="chart_header col-md-3">Your average</div>
        <div class="chart_header col-md-3">Compared to other teachers</div>
    </div>
    <div class="row subheaders">
        <div class="chart_subheader col-md-6 first">Survey question</div>
        <div class="chart_subheader col-md-3" style="position:relative">
            <div class="chart_subheader_num">0</div>
            <div class="chart_subheader_num" style="left:60px">1</div>
            <div class="chart_subheader_num" style="left:112px">2</div>
            <div class="chart_subheader_num" style="left:161px">3</div>
            <div class="chart_subheader_num" style="left:210px">4</div>
            <div class="chart_subheader_num" style="left:260px">5</div>
        </div>
        <div class="chart_subheader col-md-3 quintiles">
            <div class="quintile left">Lowest Quintile</div>
            <div class="quintile right">Highest Quintile</div>
        </div>
    </div>
    @foreach($questions as $question)
        <div class="row questions">
            <div class="col-md-6">{{$question->question}}</div>
            <div class="col-md-3">{{$question->youraverage}}</div>
            <div class="col-md-3">{{$question->comparetoothers}}</div>
        </div>
    @endforeach

@stop
