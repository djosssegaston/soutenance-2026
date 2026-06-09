//FULL CALENDAR

document.addEventListener('DOMContentLoaded', function() {
    var containerEl = document.getElementById('external-events');
    new FullCalendar.Draggable(containerEl, {
        itemSelector: '.fc-event',
        eventData: function(eventEl) {
            return {
                title: eventEl.innerText.trim(),
                title: eventEl.innerText,
                className: eventEl.className + ' overflow-hidden '
            }
        }
    });
    var calendarEl = document.getElementById('calendar2');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        // defaultView: 'month',
        navLinks: true, // can click day/week names to navigate views
        businessHours: true, // display business hours
        editable: true,
        selectable: true,
        selectMirror: true,
        droppable: true, // this allows things to be dropped onto the calendar
        select: function(arg) {
            var title = prompt('Event Title:');
            if (title) {
                calendar.addEvent({
                    title: title,
                    start: arg.start,
                    end: arg.end,
                    allDay: arg.allDay
                })
            }
            calendar.unselect()
        },
        eventClick: function(arg) {
            if (confirm('Are you sure you want to delete this event?')) {
                arg.event.remove()
            }
        },
        editable: true,
        dayMaxEvents: true, // allow "more" link when too many events
        events: [{
            title: 'Business Lunch',
            start: '2024-03-05T12:30:00',
            constraint: 'businessHours'
        }, {
            title: 'Meeting',
            start: '2024-03-15T14:00:00',
            constraint: 'availableForMeeting', // defined below
            color: '#f35e90'
        }, {
            title: 'Conference',
            start: '2024-07-25',
            end: '2024-07-27',
            color: '#e67e22'
        }, {
            title: 'Party',
            start: '2024-07-30T19:00:00',
            color: '#22c865'
        },
        // areas where "Meeting" must be dropped
        {
            id: 'availableForMeeting',
            start: '2024-03-12T09:00:00',
            end: '2024-03-12T15:00:00',
            rendering: 'background',
            color: '#5e72e4'
        }, {
            id: 'availableForMeeting',
            start: '2024-03-15T09:00:00',
            end: '2024-03-15T15:00:00',
            rendering: 'background'
        },
        // red areas where no events can be dropped
        {
            start: '2024-03-20',
            end: '2024-03-24',
            overlap: false,
            rendering: 'background',
            color: 'rgba(0,0,0,0.1)'
        }, {
            start: '2024-03-28',
            end: '2024-03-31',
            overlap: false,
            rendering: 'background',
            color: 'rgba(0,0,0,0.1)'
        }
    ]
    
    });

    calendar.render();
});

//LIST FULLCALENDAR
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'listDay,listWeek'
        },

        // customize the button names,
        // otherwise they'd all just say "list"
        views: {
            listDay: { buttonText: 'list day' },
            listWeek: { buttonText: 'list week' }
        },
        initialView: 'listWeek',
        initialDate: '2024-03-12',
        navLinks: true, // can click day/week names to navigate views
        editable: true,
        // eventLimit: true, // allow "more" link when too many events
        dayMaxEvents: true, // allow "more" link when too many events
        events: [{
            title: 'Enchanted Garden Gala',
            start: '2024-11-01'
        }, {
            title: 'Journey Through Time Exhibit',
            start: '2024-01-07',
            end: '2024-03-10'
        }, {
            id: 999,
            title: 'Twilight Masquerade Ball',
            start: '2024-11-09T16:00:00'
        }, {
            id: 999,
            title: 'Firefly Dance Spectacle',
            start: '2024-11-16T16:00:00'
        }, {
            title: 'Innovation Summit',
            start: '2024-01-11',
            end: '2024-11-13'
        }, {
            title: 'Leadership Forum',
            start: '2024-01-12T10:30:00',
            end: '2024-11-12T12:30:00'
        }, {
            title: 'Culinary Delights Luncheon',
            start: '2024-11-12T12:00:00'
        }, {
            title: 'Creative Minds Meetup',
            start: '2024-11-12T14:30:00'
        }, {
            title: 'Sunset Soiree Networking',
            start: '2024-11-12T17:30:00'
        }, {
            title: 'Elegant Evening Banquet',
            start: '2024-11-12T20:00:00'
        }, {
            title: 'Whimsical Birthday Bash',
            start: '2024-11-13T07:00:00'
        }, {
            title: 'Explore More',
            url: 'http://google.com/',
            start: '2024-11-28'
        }]
        
        
    });

    calendar.render();
});