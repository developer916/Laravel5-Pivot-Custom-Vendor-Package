@extends('layout-basic')

@section('content')

    <div id="resourceInfo">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-pivot">
                    <div class="panel-heading">Resources</div>
                    <div class="panel-body">
                        <p>
                            Doing the survey yourself is an opportunity to reflect on your teaching practice. Your self
                            assessment scores will be displayed alongside the feedback from your students in the
                            Detailed Question Breakdown report. All these results are confidential and will only be
                            available to you. Note: your students will answer the same questions as below, but in a
                            randomised order and without the AITSL categories.
                        </p>
                        <p>
                            Complete the 25 questions below and press submit to record them.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($errors->any())
        @foreach($errors->getMessages() as $this_error)
            <div class="alert-danger">{{$this_error[0]}}</div>
        @endforeach
    @endif

    {{Form::open(array('route' => 'report.self_assessment.store', 'method' => 'post')) }}
    <div id="quick_look" class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="group_domain col-xs-12">Standard 1:</div>
                <div class="group_domain_txt col-xs-10">Know students and how they learn</div>
                <div class="question question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q1</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I treat my students with respect.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q1]', '1', (Input::old('value.q1') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q1]', '2', (Input::old('value.q1') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q1]', '3', (Input::old('value.q1') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q1]', '4', (Input::old('value.q1') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q1]', '5', (Input::old('value.q1') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q2</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I care about my students' point of view.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q2]', '1', (Input::old('value.q2') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q2]', '2', (Input::old('value.q2') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q2]', '3', (Input::old('value.q2') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q2]', '4', (Input::old('value.q2') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q2]', '5', (Input::old('value.q2') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q3</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I design my classes to keep my students' engaged.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q3]', '1', (Input::old('value.q3') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q3]', '2', (Input::old('value.q3') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q3]', '3', (Input::old('value.q3') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q3]', '4', (Input::old('value.q3') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q3]', '5', (Input::old('value.q3') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q4</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I model different ways/strategies for learning new concepts.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q4]', '1', (Input::old('value.q4') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q4]', '2', (Input::old('value.q4') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q4]', '3', (Input::old('value.q4') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q4]', '4', (Input::old('value.q4') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q4]', '5', (Input::old('value.q4') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q5</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I know when my class understands, and when they do not. </div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q5]', '1', (Input::old('value.q5') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q5]', '2', (Input::old('value.q5') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q5]', '3', (Input::old('value.q5') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q5]', '4', (Input::old('value.q5') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q5]', '5', (Input::old('value.q5') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">&nbsp;</div>
            <div class="col-xs-12">
                <div class="group_domain col-xs-12">Standard 2:</div>
                <div class="group_domain_txt col-xs-10">Know the content and how to teach it</div>
                <div class="question question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q6</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I am knowledgeable about the topics in my subject. </div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q6]', '1', (Input::old('value.q6') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q6]', '2', (Input::old('value.q6') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q6]', '3', (Input::old('value.q6') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q6]', '4', (Input::old('value.q6') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q6]', '5', (Input::old('value.q6') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q7</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I am able to explain difficult concepts clearly.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q7]', '1', (Input::old('value.q7') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q7]', '2', (Input::old('value.q7') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q7]', '3', (Input::old('value.q7') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q7]', '4', (Input::old('value.q7') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q7]', '5', (Input::old('value.q7') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>

                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q8</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I push my students to set challenging learning goals. </div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q8]', '1', (Input::old('value.q8') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q8]', '2', (Input::old('value.q8') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q8]', '3', (Input::old('value.q8') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q8]', '4', (Input::old('value.q8') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q8]', '5', (Input::old('value.q8') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q9</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I help students to build their vocabulary in my class.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q9]', '1', (Input::old('value.q9') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q9]', '2', (Input::old('value.q9') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q9]', '3', (Input::old('value.q9') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q9]', '4', (Input::old('value.q9') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q9]', '5', (Input::old('value.q9') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q10</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I make what we are learning in this class interesting.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q10]', '1', (Input::old('value.q10') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q10]', '2', (Input::old('value.q10') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q10]', '3', (Input::old('value.q10') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q10]', '4', (Input::old('value.q10') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q10]', '5', (Input::old('value.q10') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">&nbsp;</div>
            <div class="col-xs-12">
                <div class="group_domain col-xs-12">Standard 3:</div>
                <div class="group_domain_txt col-xs-10">Plan for and implement effective teaching and learning</div>
                <div class="question question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q11</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I push my students to think instead of just giving me the answer. </div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q11]', '1', (Input::old('value.q11') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q11]', '2', (Input::old('value.q11') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q11]', '3', (Input::old('value.q11') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q11]', '4', (Input::old('value.q11') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q11]', '5', (Input::old('value.q11') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q12</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I ask my students to explain their answers - why they think what they think.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q12]', '1', (Input::old('value.q12') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q12]', '2', (Input::old('value.q12') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q12]', '3', (Input::old('value.q12') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q12]', '4', (Input::old('value.q12') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q12]', '5', (Input::old('value.q12') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q13</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I explain to my students why they are learning what they are learning.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q13]', '1', (Input::old('value.q13') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q13]', '2', (Input::old('value.q13') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q13]', '3', (Input::old('value.q13') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q13]', '4', (Input::old('value.q13') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q13]', '5', (Input::old('value.q13') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q14</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I give my students time to explain their ideas.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q14]', '1', (Input::old('value.q14') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q14]', '2', (Input::old('value.q14') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q14]', '3', (Input::old('value.q14') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q14]', '4', (Input::old('value.q14') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q14]', '5', (Input::old('value.q14') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q15</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">My students know what they are supposed to do in my class. </div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q15]', '1', (Input::old('value.q15') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q15]', '2', (Input::old('value.q15') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q15]', '3', (Input::old('value.q15') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q15]', '4', (Input::old('value.q15') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q15]', '5', (Input::old('value.q15') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">&nbsp;</div>
            <div class="col-xs-12">
                <div class="group_domain col-xs-12">Standard 4:</div>
                <div class="group_domain_txt col-xs-10">Create and maintain safe and supportive learning environments</div>
                <div class="question question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q16</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I use technology in my class to help my students learn.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q16]', '1', (Input::old('value.q16') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q16]', '2', (Input::old('value.q16') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q16]', '3', (Input::old('value.q16') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q16]', '4', (Input::old('value.q16') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q16]', '5', (Input::old('value.q16') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q17</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">In this class, my students are well behaved.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q17]', '1', (Input::old('value.q17') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q17]', '2', (Input::old('value.q17') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q17]', '3', (Input::old('value.q17') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q17]', '4', (Input::old('value.q17') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q17]', '5', (Input::old('value.q17') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q18</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I encourage students to share their ideas or opinions about what they are learning in my class. </div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q18]', '1', (Input::old('value.q18') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q18]', '2', (Input::old('value.q18') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q18]', '3', (Input::old('value.q18') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q18]', '4', (Input::old('value.q18') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q18]', '5', (Input::old('value.q18') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q19</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I make sure that my class is busy learning and doesn�t waste time.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q19]', '1', (Input::old('value.q19') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q19]', '2', (Input::old('value.q19') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q19]', '3', (Input::old('value.q19') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q19]', '4', (Input::old('value.q19') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q19]', '5', (Input::old('value.q19') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q20</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">My students feel comfortable asking me for individual help with what they are learning in my class.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q20]', '1', (Input::old('value.q20') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q20]', '2', (Input::old('value.q20') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q20]', '3', (Input::old('value.q20') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q20]', '4', (Input::old('value.q20') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q20]', '5', (Input::old('value.q20') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="group_domain col-xs-12">Standard 5:</div>
                <div class="group_domain_txt col-xs-10">Assess, provide feedback and report on student learning</div>
                <div class="question question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q21</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">My students understand how their work will be assessed in my class.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q21]', '1', (Input::old('value.q21') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q21]', '2', (Input::old('value.q21') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q21]', '3', (Input::old('value.q21') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q21]', '4', (Input::old('value.q21') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q21]', '5', (Input::old('value.q21') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q22</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">The comments I give on student work in this class help them to understand how to improve.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q22]', '1', (Input::old('value.q22') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q22]', '2', (Input::old('value.q22') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q22]', '3', (Input::old('value.q22') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q22]', '4', (Input::old('value.q22') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q22]', '5', (Input::old('value.q22') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q23</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">My students know how well they are doing in my class. </div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q23]', '1', (Input::old('value.q23') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q23]', '2', (Input::old('value.q23') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q23]', '3', (Input::old('value.q23') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q23]', '4', (Input::old('value.q23') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q23]', '5', (Input::old('value.q23') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q24</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">I push my students to correct their mistakes.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q24]', '1', (Input::old('value.q24') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q24]', '2', (Input::old('value.q24') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q24]', '3', (Input::old('value.q24') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q24]', '4', (Input::old('value.q24') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q24]', '5', (Input::old('value.q24') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
                <div class="question col-xs-12">
                    <div class="col-xs-1 question_number text-right" style="font-size:18px">Q25</div>
                    <div class="col-xs-10 question_block">
                        <div class="question_text">At the end of each lesson, I review what they've just learned.</div>
                    </div>
                    <div class="col-xs-11 col-xs-offset-1">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-default">
                                {{Form::radio('value[q25]', '1', (Input::old('value.q25') == 1))}} Strongly Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q25]', '2', (Input::old('value.q25') == 2))}} Disagree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q25]', '3', (Input::old('value.q25') == 3))}} Neither Disagree or Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q25]', '4', (Input::old('value.q25') == 4))}} Agree
                            </label>
                            <label class="btn btn-default">
                                {{Form::radio('value[q25]', '5', (Input::old('value.q25') == 5))}} Strongly Agree
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="assessment_id" value="{{$assessment->id}}"/>
        <input type="submit" value='Submit' class="btn btn-default btn-lg" id='submit'>
    </div>
    {{Form::close()}}

    <script type="text/javascript">
        $(':input:checked').parent('.btn').addClass('active');
    </script>
@stop

