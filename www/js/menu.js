// ApiKey : AIzaSyCF_2be1DdAKXdTsG0U30K-SHL3aW32cJY
// URL : https://calendar.google.com/calendar?cid=c3VqdnN2cjUxZDZ1ZWlyMzdrZDhudG0ybmdAZ3JvdXAuY2FsZW5kYXIuZ29vZ2xlLmNvbQ



/*
$(document).ready(function() {
    var calendarEl = $('#calendar');


*/

/*    var calendar = new FullCalendar.Calendar(calendarEl, {
        navLinks: true, // can click day/week names to navigate views
        plugins: ['dayGrid', 'list', 'googleCalendar', 'interaction'],
        eventLimit: true, // allow "more" link when too many events
        events: [{
                title: 'All Day Event',
                start: '2019-05-01'
            },
            {
                title: 'Long Event',
                start: '2019-05-07',
                end: '2019-05-10'
            },
            {
                id: 999,
                title: 'Repeating Event',
                start: '2019-05-09T16:00:00'
            },
            {
                id: 999,
                title: 'Repeating Event',
                start: '2019-05-16T16:00:00'
            },
            {
                title: 'Conference',
                start: '2019-05-11',
                end: '2019-05-13'
            },
            {
                title: 'Meeting',
                start: '2019-05-12T10:30:00',
                end: '2019-05-12T12:30:00'
            },
            {
                title: 'Lunch',
                start: '2019-05-12T12:00:00'
            },
            {
                title: 'Meeting',
                start: '2019-05-12T14:30:00'
            },
            {
                title: 'Happy Hour',
                start: '2019-05-12T17:30:00'
            },
            {
                title: 'Dinner',
                start: '2019-05-12T20:00:00'
            },
            {
                title: 'Birthday Party',
                start: '2019-05-12T07:00:00'
            },
            {
                title: 'Click for Google',
                url: 'https://google.com/',
                start: '2019-05-28'
            }
        ],

*/

$(document).ready(function() {
    //document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ['dayGrid', 'list', 'googleCalendar', 'interaction', 'timeGrid'],
        header: {
            left: 'prev, next today',
            center: 'title',
            right: 'month,basicWeek,basicDay,dayGridMonth,listYear'
        },
        eventLimit: true, // allow "more" link when too many events
        //googleCalendarApiKey: 'AIzaSyCF_2be1DdAKXdTsG0U30K-SHL3aW32cJY',
        //events: {
        //    googleCalendarId: 'sujvsvr51d6ueir37kd8ntm2ng@group.calendar.google.com'
        //},
        events: {
            url: 'ajax/feed.php',
            method: 'POST',
        },
        buttonText: {
            today: 'Aujourd\'hui',
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
            list: 'Liste'
        },
        //defaultView: 'listMonth',
        locale: 'fr',
        selectable: true,
        editable: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        dateClick: function(info) {
            console.log("DateClick:");
            console.log('Current view: ' + info.view.type);
            console.log(info);
            console.log('clicked ' + info.dateStr);
        },
        select: function(info) {
            console.log(info);
            console.log('selected ' + info.startStr + ' to ' + info.endStr);
        },
        eventClick: function(info) {
            console.log('eventClick');
            console.log(info);
        }
    });

    calendar.render();

    // Menu
    $("#Logout").click(function() {
        console.log("Logout");
        $.post("ajax/menu.php", "Action=Logout", function(data, status) {
            console.log("Data: " + data + "\nStatus: " + status);
            if (data == "OUT") {
                location.reload();
            }
        });
    });
});