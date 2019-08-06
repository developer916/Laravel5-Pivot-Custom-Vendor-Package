@extends('layout-basic')

@section('content')

    <div id="resourceInfo">
        <div class="row">
            <div class="col-sm-8">
                <div class="panel panel-pivot">
                    <div class="panel-heading">Resources</div>
                    <div class="panel-body">
                        <p>The key factor in determining how much students learn is not class size, not technology, not testing,
                            but the quality of the teacher. Your Pivot report identifies your individual areas of strength as well as
                            areas for development.</p>
                        <p>You can read more about how to interpret and make the most your Pivot results
                            <strong><a href="{{URL::asset('assets/pdf/resources/pivot_reports.pdf')}}">here</a></strong>.
                            Importantly, feedback is not an end in itself, and should be the basis for positive change and growth.</p>
                        <p>The resources on this page support each question on the Pivot survey and include research about why the
                            question is important, as well as strategies you can take into your classroom.</p>
                        <p>We will be sharing suggestions for improving teaching practice on <a href="http://pivotpl.com/" target="_blank">our website</a>,
                            <a href="https://www.facebook.com/PivotProfessionalLearning" target="_blank">Facebook</a> and
                            <a href="https://twitter.com/Pivot_PL" target="_blank">Twitter</a>.
                            Connect with us for updates!</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="panel panel-pivot panel-danger">
                    <div class="panel-heading">
                        <div class="bulb">Email us your ideas and suggestions</div>
                    </div>
                    <div class="panel-body">
                        <p>These resources are working documents and, as such, your feedback, thoughts and ideas
                            are welcome - email us at
                            <strong><a href="mailto:info@pivotpl.com">info@pivotpl.com</a></strong></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="panel panel-pivot panel-danger">
                    <div style="background-color:#5784b0" class="panel-heading">
                        <div>We need you!</div>
                    </div>
                    <div style="background-color:#dde6ef" class="panel-body">
                        <p>We're releasing a video library of the best teaching strategies around. <a href="mailto:info@pivotpl.com">Contact us</a></strong> if you'd like to be one of our educator stars in 2016!
                    </div>
                </div>
            </div>
        </div>
    </div>

<div id="quick_look" class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="group_domain col-xs-12">Standard 1:</div>
            <div class="group_domain_txt col-xs-10">Know students and how they learn</div>
            <div class="question question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q1</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q1.pdf')}}">This teacher treats me with respect.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q2</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q2.pdf')}}">This teacher cares about students' point of view.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q3</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q3.pdf')}}">This class keeps my attention - I don't get bored.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q4</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q4.pdf')}}">This teacher models different ways/strategies for learning new concepts.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q5</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q5.pdf')}}">This teacher knows when the class understands, and when we do not.</a></div>
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
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q6.pdf')}}">This teacher is knowledgeable about the topics in this subject.</a> </div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q7</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q7.pdf')}}">This teacher explains difficult things clearly.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q8</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q8.pdf')}}">This teacher pushes me to set challenging learning goals.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q9</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q9.pdf')}}">In this class, the teacher helps me to build my vocabulary.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q10</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q10.pdf')}}">This teacher makes what we are learning interesting.</a></div>
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
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q11.pdf')}}">This teacher pushes me to think instead of just giving me the answers.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q12</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q12.pdf')}}">This teacher asks me to explain my answers - why I think what I think.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q13</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q13.pdf')}}">This teacher explains why we are learning what we are learning.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q14</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q14.pdf')}}">This teacher gives us time to explain our ideas.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q15</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q15.pdf')}}">I know what I am supposed to do in this class.</a></div>
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
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q16.pdf')}}">This teacher's use of technology helps me learn in this class.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q17</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q17.pdf')}}">In this class, the students are well behaved.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q18</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q18.pdf')}}">This teacher encourages me to share my ideas or opinions about what we are learning in the class.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q19</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q19.pdf')}}">Our class is busy learning and doesn't waste time.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q20</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q20.pdf')}}">I feel comfortable asking this teacher for individual help about the things we are learning.</a></div>
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
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q21.pdf')}}">I understand how my work will be assessed in this class.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q22</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q22.pdf')}}">The comments that I get on my work in this class help me understand how to improve.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q23</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q23.pdf')}}">I know how well I am doing in this class.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q24</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q24.pdf')}}">This teacher pushes me to correct my mistakes.</a></div>
                </div>
            </div>
            <div class="question col-xs-12">
                <div class="col-xs-1 question_number text-right" style="font-size:18px">Q25</div>
                <div class="col-xs-10 question_block">
                    <div class="question_text"><a href="{{URL::asset('assets/pdf/resources/questions/q25.pdf')}}">At the end of each lesson, this teacher reviews what we have just learned.</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop


