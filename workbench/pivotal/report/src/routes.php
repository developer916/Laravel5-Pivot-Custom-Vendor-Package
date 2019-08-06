<?php
Route::group(array('before' => 'auth'), function () {


    Route::get('reports/self_assessment', array('uses' => 'Pivotal\Report\Controllers\SelfAssessmentController@view', 'before' => 'auth', 'as' => 'report.self_assessment.view'));
    Route::post('reports/self_assessment', array('uses' => 'Pivotal\Report\Controllers\SelfAssessmentController@store', 'before' => 'auth', 'as' => 'report.self_assessment.store'));

    Route::group(array('before' => 'cycle'), function () {

        //question breakdown
        Route::get('reports/question_break_down_teacher/{user}/{cycle}/{mode?}', array('uses' => 'Pivotal\Report\Controllers\QuestionBreakdownController@teacher_page', 'before' => 'auth', 'as' => 'report.question_breakdown'));
        Route::get('reports/question_break_down_school_admin/{user}/{cycle}/{mode?}', array('uses' => 'Pivotal\Report\Controllers\QuestionBreakdownController@principal_page', 'before' => 'auth', 'as' => 'report.question_breakdown'));
        Route::get('reports/question_break_down_department_head/{department}/{cycle}/{mode?}', array('uses' => 'Pivotal\Report\Controllers\QuestionBreakdownController@department_head_page', 'before' => 'auth', 'as' => 'report.question_breakdown'));
        // download csv
        Route::get('reports/full/csv/{school}/{cycle?}', array('uses' => 'Pivotal\Report\Controllers\QuestionBreakdownController@csv', 'before' => 'administrator', 'as' => 'report.csv'));

        // scatter plot
        Route::get('reports/scatter_plot_school_admin/{user}/{cycle}/{mode?}', array('uses' => 'Pivotal\Report\Controllers\ScatterPlotController@principal_page', 'before' => 'auth', 'as' => 'report.scatter_plot'));
        Route::get('reports/scatter_plot_department_head/{department}/{cycle}/{mode?}', array('uses' => 'Pivotal\Report\Controllers\ScatterPlotController@department_head_page', 'before' => 'auth', 'as' => 'report.scatter_plot'));


        //bar graph
        Route::get('reports/bar_graph_school_admin/{user}/{cycle}/{mode?}', array('uses' => 'Pivotal\Report\Controllers\BarGraphController@principal_page', 'before' => 'auth', 'as' => 'report.bar_graph'));
        Route::get('reports/bar_graph_department_head/{department}/{cycle}/{mode?}', array('uses' => 'Pivotal\Report\Controllers\BarGraphController@department_head_page', 'before' => 'auth', 'as' => 'report.bar_graph'));

        // heatmap
        Route::get('reports/heatmap_school_admin/{user}/{cycle}', array('uses' => 'Pivotal\Report\Controllers\HeatmapController@principal_page', 'before' => 'auth', 'as' => 'report.heat_map'));
        Route::get('reports/heatmap_department_head/{department}/{cycle}', array('uses' => 'Pivotal\Report\Controllers\HeatmapController@department_head_page', 'before' => 'auth', 'as' => 'report.heat_map'));
        Route::get('reports/heatmap_teacher/{user}/{cycle}', array('uses' => 'Pivotal\Report\Controllers\HeatmapController@teacher_page', 'before' => 'auth', 'as' => 'report.heat_map'));

        //comparison table
        Route::get('reports/comparison_table_school_admin/{user}/{cycle}/{mode?}', array('uses' => 'Pivotal\Report\Controllers\ComparisonTableController@principal_page', 'before' => 'auth', 'as' => 'report.comparison_table'));
    });
});