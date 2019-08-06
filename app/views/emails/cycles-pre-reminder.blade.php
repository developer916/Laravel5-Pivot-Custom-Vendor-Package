<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<div>
    <strong>
        Dear {{ $teacher->name }}
    </strong>
</div>
<div>
    <p>This is a reminder that the Pivot student survey at {{ $teacher->school->name }} is scheduled to
        finish on <strong>{{ $cycle->end_date }}</strong>.</p>
    <p>Your survey results are based on your students' feedback. If your response rates are low, you
        should prompt your classes to complete the survey now. <strong>Note:</strong> where there are fewer than 5
        responses, a result will not generate for that class.</p>
    <p>The response rates for each of your classes are as follows:</p>
</div>
<table style="width:100%; border-top: 1px solid #ddd;" class="table table-condensed table-hover">
    <tr style="text-align: left;">
        <th style="padding: 8px;">Class name</th>
        <th style="padding: 8px;">Class code</th>
        <th style="padding: 8px;">Number of students</th>
        <th style="padding: 8px;">Class Survey link</th>
        <th style="padding: 8px;">Responses</th>
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
            <td style="border-top: 1px solid #ddd; padding: 8px;">
                {{ $class->pivot->responses }} / {{ $class->num_students }}
            </td>
        </tr>
    @endforeach
</table>
<div>
    <p>The day after the survey closes, you can retrieve your results reports by logging in to the Pivot
        portal at <a href="http://results.pivotpl.com">http://results.pivotpl.com</a> using your school email
        address as the username and your Pivot password (there is a
        <a href="https://results.pivotpl.com/password/remind">link to reset the password</a> if you have forgotten it).
        Your individual results are confidential to you.</p>
    <p>Please be sure to go to the resources menu on the Pivot portal for guides on how to read your report, as
        well as research, strategies and further reading that directly supports your individual results.</p>
    <p>Join the conversation on teaching best practice via
        <a href="https://www.facebook.com/PivotProfessionalLearning">Facebook</a>,
        <a href="https://twitter.com/Pivot_PL">Twitter</a> or email us with your feedback or suggestions at
        <a href="mailto:help@pivotpl.com">help@pivotpl.com</a>.</p>
    <p>Any questions? Contact <a href="mailto:help@pivotpl.com">help@pivotpl.com</a> or your school administrator.</p>
    <p>The Pivot Team.</p>
</div>
</body>
</html>