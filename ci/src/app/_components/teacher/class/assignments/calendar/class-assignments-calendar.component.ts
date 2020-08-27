import {Component, OnInit, OnDestroy, Input, Output, ViewChild, EventEmitter} from '@angular/core';
import {CalendarOptions, FullCalendarComponent} from '@fullcalendar/angular';
import * as $ from 'jquery';
import * as moment from 'moment';

@Component({
    selector: 'app-class-assignments-calendar',
    templateUrl: './class-assignments-calendar.component.html',
    styleUrls: ['./class-assignments-calendar.component.scss'],
    providers: []
})
export class ClassAssignmentsCalendarComponent implements OnInit, OnDestroy {

    @Input() assignments;
    @Input() available_assignments;
    currentDate = (new Date()).toISOString().split('T')[0];

    @Output() onAssignmentDateChanged = new EventEmitter<string[]>();
    @Output() onAssignmentAddClicked = new EventEmitter<any>();
    @Output() onAssignmentEditClicked = new EventEmitter<number>();

    @ViewChild('calendar') calendarComponent: FullCalendarComponent;

    calendarOptions: CalendarOptions = {
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        firstDay: 1,
        dayMaxEventRows: 3,
        editable: true,
        eventStartEditable: true,
        eventResizableFromStart: true,
        eventDurationEditable: true,
        // droppable: true,
        // eventResourceEditable: true,
        navLinks: true,
        selectable: true,
        events: [],
        eventColor: 'rgba(0, 38, 66, 0.7)',
        views: {
            timeGrid: {
                allDaySlot: false,
                nowIndicator: true,
                dayMaxEventRows: 5
            }
        },
        eventClick: this.handleEventClick.bind(this),
        eventDrop: this.handleEventDrop.bind(this),
        eventResize: this.handleEventDrop.bind(this),
        select: this.handleSelect.bind(this),
        unselect: this.handleUnselect.bind(this),
        // eventContent : { html: '<i>some html</i>' }
    };

    start = null;
    end = null;
    appId = null;

    constructor() {}

    ngOnInit() {
        this.updateCalendarEvents();
    }

    ngOnDestroy() {
        // this.calendarComponent.destroy();
    }

    updateCalendarEvents() {
        const newEvents = [];
        this.assignments.forEach( app => {
            const startAt = app.start_time ? (app.start_date + 'T' + app.start_time) : app.start_date;
            const dueAt = app.due_time ? (app.due_date + 'T' + app.due_time) : app.due_date;
            const event = {
                id: app.id,
                title: app.name,
                start: app.start_date ? startAt : this.currentDate,
                end: app.due_date ? dueAt : '2100-01-01',
                color: app.color ? app.color : 'rgba(0, 38, 66, 0.7)',
                allDay: !app.due_time && !app.start_time
            };
            newEvents.push(event);
        });
        this.calendarOptions.events = newEvents;
        /* try {
            ($('#calendar') as any).fullCalendar('renderEvents', newEvents, true);
        } catch (e) {} */
    }

    handleEventDrop(arg) {
        this.onAssignmentDateChanged.emit(arg.event);
    }

    handleSelect(info) {
        if (this.available_assignments.length > 0) {
            this.start = info.start;
            this.end = info.end;
            const x = (info.jsEvent.pageX - $('#calendar-container').offset().left);
            const y = (info.jsEvent.pageY - $('#calendar-container').offset().top);
            $('#add-button').css( {
                display: 'block',
                position: 'absolute',
                top: (y - 20) + 'px',
                left: (x - 20) + 'px'
            });
        }
    }

    handleUnselect() {
        $('#add-button').css( {
            display: 'none'
        });
    }

    handleEventClick(arg) {
        this.appId = +arg.event.id;
        const x = (arg.jsEvent.pageX - $('#calendar-container').offset().left);
        const y = (arg.jsEvent.pageY - $('#calendar-container').offset().top);
        $('#edit-button').css( {
            display: 'block',
            position: 'absolute',
            top: (y - 20) + 'px',
            left: (x - 20) + 'px'
        });
        setTimeout(() => {
            $('#edit-button').css( {
                display: 'none'
            });
        }, 3000);
    }

    addEvent() {
        this.onAssignmentAddClicked.emit({
            start: this.start,
            end: this.end
        });
    }

    editEvent() {
        this.onAssignmentEditClicked.emit(this.appId);
        $('#edit-button').css( {
            display: 'none'
        });
    }

}
