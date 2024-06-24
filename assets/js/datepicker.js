$(document).ready(function ($) {
    $('#datepicker').datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years",
        autoclose: true,
        clearBtn: true,
        todayBtn: 'linked',
        todayHighlight: true,
        // daysOfWeekDisabled: '06', //String, Array. Default: []. disable weekends: '06' or '0,6' or [0,6].
        // format: 'yyyy-mm-dd',
        // format: 'dd MM yyyy',
        // format: {
        //     toDisplay: function(date, format, language) {
        //         return date.toLocaleDateString("id-ID", {
        //             day: "2-digit",
        //             month: "long",
        //             year: "numeric",
        //         });
        //     },
        //     toValue: function(date, format, language) {
        //         return date
        //     }
        // },
        // startDate: '0d',
        // defaultViewDate: new Date(),
    }); //.datepicker("setDate", new Date());
});
