<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<div>
    <strong>
        Welcome to the Pivot student survey!
    </strong>
</div>
<div>
    <strong>Why am I receiving this email?</strong>
</div>
<div>
    <p>Your school administrator has nominated one or more of your classes to conduct an online student survey opening
        on {{$cycle->start_date}} and closing on {{$cycle->end_date}}. The survey is designed to give you feedback on your teaching
        practice.
    </p>
    <p>
        You can view the survey and test it yourself by logging on, using your school email address and your Pivot
        password, at <a href="http://results.pivotpl.com">http://results.pivotpl.com</a> and clicking on 'Survey Preview' on your home page. (If you have
        forgotten your Pivot password, click directly on 'Reset my password' on <a href="http://results.pivotpl.com">http://results.pivotpl.com</a> and follow
        the instructions).
    </p>

    <p>
        In the table below you can see your classes to be surveyed, and the class survey link. <strong>Note:</strong>
        Each class has its own unique link (the questions asked in the survey are the same).
    </p>
</div>
<table style="width:100%; border-top: 1px solid #ddd;" class="table table-condensed table-hover">
    <tr style="text-align: left;">
        <th style="padding: 8px;">Class name</th>
        <th style="padding: 8px;">Class code</th>
        <th style="padding: 8px;">Number of students</th>
        <th style="padding: 8px;">Class Survey link</th>
    </tr>
    @foreach ($classes as $class)
        <tr>
            <td style="border-top: 1px solid #ddd; padding: 8px;">
                {{ $class->name }}
            </td>
            <td style="border-top: 1px solid #ddd; padding: 8px;">
                {{ $class->code }}
            </td>
            <td style="border-top: 1px solid #ddd; padding: 8px;">
                {{ $class->num_students }}
            </td>
            <td style="border-top: 1px solid #ddd; padding: 8px;">
                <a href="{{ $class->pivot->url }}" target="_blank" title="Go to survey">
                    {{ $class->pivot->url }}
                </a>
            </td>
        </tr>
    @endforeach
</table>
<div>
    <strong>What do I have to do?</strong>

    <p>1. Introduce the survey to your students, setting aside 10 minutes at the end of each class to do the survey. A
        draft script is written out below that you may wish to draw on.</p>

    <p>2. Identify the unique class survey link from the table above for your class and provide it to your students (we
        suggest copying and pasting the link in an email to them). It is important that the correct link be sent to the
        class.</p>

    <p>
        Note that the surveys are anoymous and no student data will be collected. Each student should only complete this
        survey once, for each class. You can check student response rates via your login at <a
                href="http://results.pivotpl.com">http://results.pivotpl.com</a>
        by clicking on the Class page.
    </p>
</div>
<div>
    <strong>What happens then?</strong>

    <p>
        As soon as the survey closes your results will be immediately available to you via your login at <a
                href="http://results.pivotpl.com">http://results.pivotpl.com</a>.
    </p>

    <p>
        Nobody else will see your results. Your Head of Department and Principal will see Department-wide and
        School-wide averages only.
    </p>
</div>
<div>
    <strong>I have a question!</strong>

    <p>
        Contact your school administrator or <a href="mailto:help@pivotpl.com">help@pivotpl.com</a> - we are always
        happy to help. (Please do not reply to this email address.)
    </p>
</div>
<p> Thank you for participating in this student survey.</p>

<p>The Pivot Team</p>
<span><a href="http://www.pivotpl.com">http://www.pivotpl.com</a></span>
<hr/>
<div>
    <strong>Suggested script for teachers to introduce the survey to their students</strong>

    <p>
        {{$teacher->school->name}} is running a new student survey designed to get real feedback from you, the students,
        about how
        you learn in this class. Please open up the survey weblink I have given you and complete the survey – there are
        25 statements and you are asked to respond as to whether you ‘Strongly Disagree’ through to ‘Strongly Agree’.
        Press ‘Submit’ at the end of the 25 questions. The survey should take you less than 10 minutes to complete, and
        please ask for help in understanding the questions if you need it. All responses are completely anonymous and
        your personal details will not be captured in the survey, so please be honest in your responses.
    </p>

    <p>
        Kind regards,
    </p>

    <p>
        {{$teacher->name}}
    </p>
</div>
</body>
</html>