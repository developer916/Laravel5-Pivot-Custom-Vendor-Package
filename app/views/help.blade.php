@extends('layout-basic')

@section('content')
<div class="row">
    <div id="navigation" class="col-md-4">
        <h3>Index</h3>
        <nav id="help-nav" class="navbar navbar-help navbar-static">
            <h4 style="padding-left:6px;">How to...</h4>
            <ul id="help-nav" class="nav nav-pills nav-stacked">
                <li><a href="#navigate">Navigate the Pivot system</a></li>
                <li><a href="#resetpass">Reset my password</a></li>
                <li><a href="#reviewclass">Review my class details</a></li>
                <li><a href="#previewsurvey">Preview my class's student survey</a></li>
                <li><a href="#surveylink">Retrieve my class survey links</a></li>
                <li><a href="#checkresponse">Check how many of my students have completed the survey</a></li>
                <li><a href="#completesa">Complete my Teacher Self Assessment</a></li>
                <li><a href="#viewreport">View my results reports</a></li>
                <li><a href="#interpretreport">Interpret my results reports</a></li>
                <li><a href="#printreport">Print my results reports</a></li>
                <li><a href="#gethelp">Get further help on accessing and using the system</a></li>
            </ul>
        </nav>

        <nav id="help-nav" class="navbar navbar-help navbar-static">
            <h4 style="padding-left:6px;">Frequently asked questions...</h4>
            <ul id="help-nav" class="nav nav-pills nav-stacked">
                <li><a href="#studentintro">How should I introduce the survey to my students?</a></li>
                <li><a href="#studentinfo">What student information is captured?</a></li>
                <li><a href="#results">Who will see my results?</a></li>
                <li><a href="#teachereffectiveness">How do you know that student survey results are reliable indicators of a teacher's effectiveness?</a></li>
                <li><a href="#studentseriousness">How do I know the students will take this survey seriously?</a></li>
                <li><a href="#surveyimportance">How important are my results in understanding how effective I am in the classroom?</a></li>
                <li><a href="#reliabilityvalidity">What statistical analysis has been done on the survey questions to ensure their reliability and validity?</a></li>
                <li><a href="#itsecure">How do I know the IT system is secure?</a></li>
            </ul>
        </nav>
    </div>
    <div id="scrolling" class="col-md-8" data-spy="scroll" data-target="#help-nav" style="overflow-y: auto">
        <h3 class="helpheader">How do I...</h3>
        <h4 id="navigate" class="helpheader">...navigate the Pivot system?</h4>
        <p>
            You can navigate the system by clicking on any heading in blue font (a hyperlink or active web link) to
            reveal further information.
            You can also navigate your information via the top row of tabs (the menu bar) - Department, Classes,
            Reports. The house icon at the far left of your menu bar will bring you back to your home page.
        </p>

        <h4 id="resetpass" class="helpheader">...reset my password?</h4>
        <p>
            To change your password, go to the login page <a href="www.results.pivotpl.com">www.results.pivotpl.com</a>, click "Reset your password", and enter your email address.
            You will receive an email with instructions as to how to create a new password.
        </p>

        <h4 id="reviewclass" class="helpheader">...review my class details?</h4>
        <p>
            Your class details have been uploaded by your School Administrator.  If you notice an error, contact your School Administrator to make any necessary changes.
        </p>
        <p>There are two ways to check your class details:</p>
        <ol>
            <li>
                Under the <strong>Departments</strong> tab on your dashboard, select your Department name. On that Department's page, you can view:
                <ol>
                    <li>the name of the Head of Department, and</li>
                    <li>a list of all teachers and classes run by that Department.</li>
                </ol>
                Click on your own name, or one of your own classes to view further information (note that your name and your own classes will show up in blue font (hyperlinked)).
                You will not be able to view any information on any other teacher or class.
            </li>
            <li>
                Under the <strong>Classes</strong> tab on your dashboard, select one of your classes.  On that class's page, you can view:
                <ul>
                    <li>the class name,</li>
                    <li>the class code,</li>
                    <li>the year level, and</li>
                    <li>the number of students.</li>
                </ul>
                At the bottom of these details on your class, you can see the 'Survey Cycles' box, which provides a link to a 'Survey Preview' (click on this link to see the survey your students will be asked to complete). The information in the box will also tell you:
                <ul>
                    <li>what survey cycles the class has been assigned to,</li>
                    <li>the unique weblink to that class's survey,</li>
                    <li>the number of respondents to the survey out of the total number of students in that class, and</li>
                    <li>the percentage of students in the class who have completed the survey (the response rate).</li>
                </ul>
            </li>
        </ol>

        <h4 id="previewsurvey" class="helpheader">...preview my class's student survey?</h4>
        <p>You can preview or test the survey that your students will be asked to complete, and your responses will not be saved against your results.</p>
        <p>Navigate to your home page (click on the house icon on the top menu bar) and scroll to the bottom of the page. Click on 'Preview the survey here'</p>

        <p>You will see that the preview instrument will require you to complete each and every question prior to being
            able to press "Submit" at the end: this is the same process that will apply to your students. Of course,
            unlike the survey your students will undertake, this preview survey will not save your responses.(To have
            your results recorded and displayed in the 'Question Breakdown' report, click instead on 'Do the Survey now'
            under the 'Teacher Self Assessment' heading.)</p>
        <p>Note that each survey will display the 25 questions in a random order, so the survey may appear different each time you preview it.</p>

        <h4 id="surveylink" class="helpheader">...retrieve my class survey links?</h4>
        <p>There are two ways you can retrieve the unique survey weblink for each of your classes:</p>
        <ol>
            <li>The day before the commencement of a survey cycle, you will receive an email from <a href="notifications@pivotpl.com">notifications@pivotpl.com</a> with a summary list of each of the unique survey weblinks for each of your classes.
                If you have not received this email into your regular inbox, check your junk mail inbox.
            </li>
            <li>Click on the <strong>Classes</strong> tab and select one of your classes from the drop-down menu.
                When you reach the page for that class, you can see a range of class details.
                Check that the details are correct - the class name, class code and number of students.
                You can see in the 'Survey Cycles' box each of the survey cycles to which this class has been assigned.
                <br/>
                Identify the right survey cycle for that class (e.g. Semester 1, 2015).  You can see:
                <ul>
                    <li>the unique weblink to that class's student survey</li>
                    <li>the number of respondents to the survey out of the total number of students in that class, and</li>
                    <li>the percentage of students in the class who have completed the survey (the response rate).</li>
                </ul>
            </li>
        </ol>
        <p>Once you have retrieved the right survey link for your class, Pivot suggests you copy and paste that link into an email to send to your students, and ask them to click on the link to complete the survey. See the FAQ "How should I introduce the survey to my students?" below for a suggested script on introducing the survey to your students.</p>


            <h4 id="checkresponse" class="helpheader">...check how many of my students have completed the survey?</h4>

            <p>
                Click on the <strong>Classes</strong> tab and select the class from the drop-down menu. When you reach
                the page for that class, you can see a range of class details.
                Check that the details are correct - the class name, class code and number of students.
                You can see in the "survey cycles" box each of the surveys that this class has been allocated to.
            </p>

            <p>
                Identify the right survey cycle for that class (e.g. Semester 1, 2015): you can see:
            </p>
            <ul>
                <li>the unique weblink to that class's student survey,</li>
                <li>the number of respondents to the survey out of the total number of students in that class, and</li>
                <li>the percentage of students in the class who have completed the survey (the response rate).</li>
            </ul>
            <p>
                Note that the number of respondents to a class survey updates in "real time". When monitoring your
                students' completion of the survey, if you find it helpful you could
                keep an eye on this completion rate (you will need to refresh your internet browser for the information
                to update).
            </p>

            <h4 id="viewreport" class="helpheader">...view my results reports? </h4>

            <p>Click on the <strong>Reports</strong> tab. Select the survey cycle you are interested in (e.g. Semester
                1, 2015). Then, you may select:</p>
            <ul>
                <li>the "Summary - Heat map" report, showing a circle heat map of all the questions, and your top/bottom
                    scoring standards and individual questions, and
                </li>
                <li>the "Detailed Question Breakdown", showing your average results for each question, for each of your
                    classes. It also showsthe distribution of scores across all classes, for each question.
                </li>
            </ul>
            <p>Note that the results reports will only be available once that survey cycle has been closed.</p>

            <p>
                Note that if fewer than five students in a class have responded to a survey, an average result <em>for
                    that individual class</em> will not be shown so as to preserve the anonymity of those students
                (the relevant section of the "Detailed Question Breakdown" report will say "N/A" for that class). These
                students' results will still be included in your average result for that question.
            </p>


        <h4 id="completesa" class="helpheader">... complete my Teacher Self Assessment</h4>
        <p>You have the option of completing the survey yourself, and having these results recorded in your Question Breakdown report.</p>
        <p>To do a Self Assessment, simply go to the homepage, scroll down, and click on 'Do the survey now' under the 'Teacher Self Assessment' heading. Note that you may only do a Self Assessment once per survey cycle, and when it has been done the notification will change from 'Incomplete' to 'Complete'.</p>

        <p>The Self Assessment is optional, and serves to reflect your own perception of your strengths and development
            opportunities, as against your students' responses - the scores will be presented side-by-side in your
            Question Breakdown report. This report, and your Self Assessment scores, will only be available to you and
            not viewable by anyone else at the school. Note that your incomplete for each survey cycle, but as described
            above, they will not have access to the results.</p>

        <p>To view the survey questions but <u>not</u> have the results recorded on your Question Breakdown Report, go
            to 'Survey Preview' heading on the home page and click on 'Preview the survey here'</p>


        <h4 id="interpretreport" class="helpheader">...interpret my results reports?</h4>
        <p>You can view two reports on your results.</p>
        <h4><strong><em>The "Summary - Heat map" report</em></strong></h4>
        <p>
            The heat map is a circle showing each of the questions, numbered from 1 to 25, as represented by a "wedge".  The "wedges" display the average score for a specific question within each of the five Standards,
            where 1=strongly disagree, and 5=strongly agree.  Hover your mouse over a "wedge" to see the question text as well as the your numerical average for that question.
        </p>
        <p>The 25 questions are grouped into the five National Professional Standards for Teaching under the "Professional Knowledge" and "Professional Practice" domains:</p>
        <ol>
            <li>Know students and how they learn</li>
            <li>Know the content and how to teach it</li>
            <li>Plan for and implement effective teaching and learning</li>
            <li>Create and maintain supportive and safe learning environments</li>
            <li>Assess, provide feedback and report on student learning</li>
        </ol>
        <p>
            On the heat map circle, you can see the school's average score for each of the five National Professional Standards (the number in the orange dot,
            and as represented by the orange dotted line on the heat map), and the purple box and solid purple line tells your average score per Standard.
        </p>
        <p>Beneath the heat map circle, you can see the following headings:</p>
        <ul>
            <li>The "Area of strength" heading describes your highest scoring Standard, and top three individual questions.</li>
            <li>The "Area for growth" heading describes the your lowest scoring Standard and bottom three individual questions.</li>
        </ul>
        <p>
            You can print this "Summary - Heat map" report by pressing the 'Print' button
        </p>
        <h4><strong><em>The "Detailed Question Breakdown" Report</em></strong></h4>
        <p>
            This report displays your average score for every question.  The 25 questions are grouped into the five National Professional Standards for
            Teaching under the "Professional Knowledge" and "Professional Practice" domains:
        </p>
        <ul>
            <li>Know students and how they learn</li>
            <li>Know the content and how to teach it</li>
            <li>Plan for and implement effective teaching and learning</li>
            <li>Create and maintain supportive and safe learning environments</li>
            <li>Assess, provide feedback and report on student learning</li>
        </ul>
        <p>You can navigate between each of the standards by clicking on the standard number tabs at the top of the page. Note that the scores range from 1 (=strongly disagree) to 5 (=strongly agree).</p>
        <p>For each question, you can see:</p>
        <ul>
            <li>your average score (in a purple box)</li>
            <li>your school's average score (in an orange circle)</li>
            <li><em>if you have done a Pivot student survey before,</em> your previous survey average score your previous survey average (in a grey box)</li>
        </ul>
        <p>
            You can also see a bar graph showing the average score for each class (represented by a column), for that question.
            An orange dotted line across the graph represents the school's average score for that question.
        </p>
        <p><em>If you have already done one Pivot student survey in the current school year</em>(That is, for the second survey in a year), the bar graph will also show the average score for each class that was surveyed in the first survey period.</p>
        <p>
            Please note that, if fewer than five students in a class have responded to a survey, an average result for that class will not be shown so as to preserve the anonymity of those students.
            In this instance, the relevant column in this bar graph will say "N/A" for that class. These students" results will however still be included in your average result for that question.
        </p>
        <p>
            The second bar graph shows the distribution of responses, for that question.  This shows the number of your students, from all classes, who have selected scores at
            each point from 1 (=strongly disagree) to 5 (=strongly agree).
        </p>
        <p>
            You can print this "Detailed Question Breakdown" report by pressing the print button on your screen.
        </p>

        <h4 id="printreport" class="helpheader">...print my results reports?</h4>
        <p>
            You can print your results reports by pressing the 'Print' button on your screen.
        </p>
        <p>If you choose to print by going to the 'File' menu on your internet browser, and pressing 'Print', for optimal printing results, print in colour and ensure that 'Print Backgrounds' is selected. If you select 'Print Header and Footer' this will also print out the internet address details.</p>

        <h4 id="gethelp" class="helpheader">...get further help on accessing and using the system?</h4>
        <p>If you have any further questions or would like help in accessing and using the system, contact <a href="help@pivotpl.com">help@pivotpl.com</a>.  You will receive a response within 24 hours (usually much sooner!).</p>

        <h3 class="helpheader">Frequently Asked Questions</h3>
        <h4 id="studentintro" class="helpheader">How should I introduce the survey to my students?</h4>
        <p>See below a suggested script for you to email to your students, or talk to, about the student survey on your teaching practice:</p>
        <p style="margin-left: 20px">
            [School/Department name] is running a new student survey designed to get real feedback from you, the students, about how you learn in this class.
            Please open up the survey weblink I have given you and complete the survey - there are 25 statements and you are asked to respond as to whether you "Strongly Disagree" through to "Strongly Agree".
            Press "Submit" at the end of the 25 questions. The survey should take you less than 10 minutes to complete, and please ask for help in understanding the questions if you need it.
            All responses are completely anonymous and your personal details will not be captured in the survey, so please be honest in your responses.<br/><br/>
            Kind regards
            [Teacher name]
        </p>

        <h4 id="studentinfo" class="helpheader">What student information is captured?</h4>
        <p>
            Absolutely no student identifying information is collected in the survey - including no names, email addresses or computer IP addresses.
            To further protect the anonymity of the students, no results will be released to teachers or others in the school for classes with fewer than five students.
        </p>

        <h4 id="results" class="helpheader">Who will see my results?</h4>
        <p>
            Pivot's survey tool and report on an individual teacher will only be provided to that teacher, as it is designed first and foremost to be a tool to assist the
            teacher reflect on their teaching practice and identify areas in which they may wish to focus their professional learning activities.
        </p>
        <p>
            Neither the Head of Department nor the Principal will see an individual teacher's results. The Head of Department will see an overall summary of the Department's results -
            aggregated teacher results, that is, averages of the responses from all students in all teachers" classes in that Year level or Department - enabling them to consider strengths and development
            opportunities in their Department.
        </p>
        <p>
            Similarly, the Principal will see an overall summary of the school's reports - averages of all teachers in each Department or Year level -
            enabling them to consider strengths and development opportunities across the school and its Departments.
        </p>
        <p>
            To further ensure the privacy of a teacher's results, no aggregated teacher results will be released where there are fewer than three teachers in any Department and/or Year level.
            This is to ensure that it is not possible to attribute or "guess" which teacher received which average score.
        </p>
        <p>
            All this being said, one of the major applications of this survey is for you to reflect on your own teaching practice and its change over time,
            and evidence has shown that teachers who do this regularly show significant improvements in their teaching practice.
        </p>
        <ul>
            <li>You are encouraged to share and discuss your results with at least one trusted person to gain further insight into your teaching strengths and development opportunities.</li>
            <li>You are also encouraged to discuss your results with your Principal, Head of Department, lead teacher and/or peers (as appropriate), to secure support for more targeted professional learning opportunities, to share ideas for improved teaching practice and learning outcomes, and to develop stronger peer and group teaching networks.</li>
            <li>Finally, emerging evidence indicates the significant value of student voice and engagement in directing their learning environment. You are encouraged to discuss your results with your students to gain further insights into their learning needs and preferences.</li>
        </ul>

        <h4 id="teachereffectiveness" class="helpheader">Are student survey results reliable indicators of a teacher's effectiveness?</h4>
        <p>
            A landmark 2008 study led by John Hattie, currently head of the Australian Institute for Teaching and School Leadership, concluded that receiving feedback on teaching practice is critical
            to effective teacher professional learning. Of this feedback, among the most powerful is from the student to the teacher, given it allows teachers to see learning through the eyes of their students.
        </p>
        <p>
            International research, including the 2013 Gates Foundation Measures of Effective Teaching Project involving over 3,000 teachers, confirms this result.
            The Measures of Effective Project found that student surveys of a teacher's effectiveness are reliable predictors of a student's learning outcomes and growth over time,
            and more reliable than classroom observations.
        </p>
        <p>
            However, student surveys comprise only one input into an overall assessment of a teacher's performance. Research by the Grattan Institute in 2011 indicated that to effectively develop
            teaching practice to improve learning outcomes, a range of indicators should be used: student surveys; student performance and assessments; peer observation and collaboration; direct
            observation of classroom teaching and learning; parent surveys; 360-degree assessment; self-assessment and external observation.
        </p>
        <p>
            Student surveys should therefore be treated as a reliable indicator of a teacher's effectiveness, but any data should be considered in the context of other indicators and measures of a teacher's practice.
        </p>
        <p>Contact <a href="mailto:info@pivotpl.com">info@pivotpl.com</a> for a detailed research paper, including full academic references, on student surveys and teacher effectiveness.</p>

        <h4 id="studentseriousness" class="helpheader">How do I know the students will take this survey seriously?</h4>
        <p>
            International research has demonstrated that student surveys can and do clearly differentiate between teacher quality: students know their teachers well.
            The Gates Foundation Measures of Effective Teaching Project found:
        </p>
        <p style="margin-left:20px;font-style:italic">
            "student perceptions in one class or one academic year predict large differences in student achievement gains in other classes taught by the same teacher.
            In other words, when students report positive classroom experiences, those classrooms tend to achieve greater learning gains, and other classrooms taught by the same teacher appear to do so as well."
        </p>
        <p>Pivot's survey questions have been specifically designed to gather feedback on key aspects of a teacher's practice that are predictive of student outcomes.</p>
        <p>
            It is true that even a great instrument for collecting student feedback can be distorted by malicious students answering untruthfully - particularly if, for example, students do not
            trust that their answers will be kept confidential. The Pivot survey has been designed specifically to ensure that no personal student data is collected, and that results will not be
            provided where there are fewer than ten student responses so as to preserve their anonymity.
        </p>
        <p>
            Also, Pivot's IT system is able to identify suspicious patterns in student responses and anything suspicious will be reported to the school leadership.
        </p>
        <p>
            Students are also likely treat the survey with greater respect and seriousness if they see that their feedback is actioned, and elicits a meaningful change in the teacher's practice.
            In introducing the survey to the students, it will be important for the teacher/invigilator to explicitly remind students that honest, thoughtful responses help the teacher, which in turn will help the student.
        </p>

        <h4 id="surveyimportance" class="helpheader">How important are my results in understanding how effective I am in the classroom?</h4>
        <p>
            Student surveys should be treated as one measure of a teacher's performance. The Pivot Student Survey draws from extensive international research on teacher effectiveness.
            Pivot has undertaken extensive analysis to ensure that the 25 individual questions, organised under five National Professional Standards for Teachers, are both valid and reliable.
        </p>
        <p>
            Your results reflect the average of all students' feedback on your teaching. The summary presents the mathematical ordering of scores against the standards and individual questions,
            each of which is focused on an aspect of teaching practice. The average score of each of the standards, and individual questions, gives a sense of your strengths and development opportunities.
            In practice, there is likely to be limited difference between scores that are especially close, and they may not be statistically significant.
            (In other words, the mathematical ordering of standards and question scores that are especially close may not be a reflection of the differences between your and other teachers" skills and capabilities.)
        </p>
        <p>The value of the data is that it captures overall themes and trends, providing you with a powerful tool to help reflect on your own teaching practice and its change over time.</p>

        <h4 id="reliabilityvalidity" class="helpheader">What statistical analysis has been done on the survey questions to ensure their reliability and validity?</h4>
        <p>
            Validity is the extent to which a tool measures what it is supposed to measure. Put a different way, the validity means the existence of a strong relationship between the
            tool and the construct (concept) it should measure.
        </p>
        <p>
            Validity is mostly a non-statistical concept: it is not proved by statistical analyses, but by a build-up of meaningful, common sense evidence.
            The validity of our Pivot Survey Tool is ensured by the fact that all the questions are based on international research on teacher effectiveness and are aligned to the National Professional Standards.
        </p>
        <ul>
            <li>
                Specifically, the Gates' Foundation Measures of Effective Teaching Project (MET Project) of 3000 teachers found that student surveys of a teacher's effectiveness are reliable predictors
                of a student's learning outcomes and growth over time, and more reliable than classroom observations. The questions in the MET Project's surveys, from which the Pivot Survey questions
                have been drawn, have been proven to correlate with other measures of student achievement (including "value-added" measures), classroom observations and other metrics of effective teaching.
            </li>
            <li>
                On the alignment of questions to the National Professional Standards, we have completed statistical analyses to ensure that items in one standard have a significant correlation with each other.
                Our analyses also show that the tool effectively differentiates between teachers.
            </li>
        </ul>
        <p>Reliability relates to a tool's capability to produce similar results under constant, stable conditions. So a measurement tool is reliable if it provides similar results when used in similar circumstances.</p>
        <p>
            To assess the reliability of our Pivot Survey Tool, we have run tests on a large sample of students and measured the internal consistency of each professional standard.
            The indicator used to evaluate the consistency of items was the Cronbach's alpha: for all the standards, the value of alpha was higher than 0.8, which indicates a very good internal consistency.
        </p>

        <h4 id="itsecure" class="helpheader">How do I know the IT system is secure?</h4>
        <p>
            Pivot Professional Learning's data security is provided and maintained through our hosting partnership with RackSpace based in Sydney,
            offering industry-leading best practice for data security. Contact <a href="info@pivotpl.com">info@pivotpl.com</a> if you would like more information about our data security policy.
        </p>
        <p>
            No one at your school, or at Pivot Professional Learning, has access to the system-generated password that you will receive via email from
            <a href="notifications@pivotpl.com">notifications@pivotpl.com</a> when your school's account is set up.
        </p>
        <p>
            If you wish to change your password to something you can more easily remember, go to <a href="www.results.pivotpl.com">www.results.pivotpl.com</a>,
            click "Reset my password" and follow the instructions to create a new password. A strong password should be:
        </p>
        <ul>
            <li>greater than 6 characters</li>
            <li>include a combination of letters, numbers and upper case (capitals)</li>
        </ul>
        <p>You should not write down your password anywhere where it may be copied or seen by others.</p>
        <p>Of course, if you think your Pivot password may be compromised (or known by others), you can reset it via <a href=www.results.pivotpl.com">www.results.pivotpl.com</a>, clicking "Reset my password" at any time.  </p>
        <p>Contact <a href="mailto:help@pivotpl.com">help@pivotpl.com</a> if you have any further concerns.</p>
    </div>
    <div id="xsmalldevice" class="device-xs visible-xs"></div>
    <div id="smalldevice" class="device-sm visible-sm"></div>
</div>

<script type="text/javascript">
    $(function() {
        if (!$('#xsmalldevice').is(':visible') && !$('#smalldevice').is(':visible')) {
            $('#scrolling').css('height', $('#navigation').height()+'px');
        }
    });
</script>

@stop


