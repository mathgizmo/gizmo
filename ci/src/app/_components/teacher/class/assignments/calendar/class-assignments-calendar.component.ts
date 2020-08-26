import {Component, OnInit, OnDestroy, Input, ViewChild} from '@angular/core';
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
    currentDate = (new Date()).toISOString().split('T')[0];

    @ViewChild('calendar') calendarComponent: FullCalendarComponent;

    calendarOptions: CalendarOptions = {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek'
        },
        firstDay: 1,
        events: [],
        eventColor: 'rgba(0, 38, 66, 0.7)',
        /* views: {
            dayGridMonth: {},
            dayGridWeek: {}
        }, */
        dateClick: this.handleDateClick.bind(this),
    };

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
                end: app.due_date ? dueAt : '2100-01-01'
                // allDay: !app.due_date
            };
            newEvents.push(event);
        });
        this.calendarOptions.events = newEvents;
        /* try {
            ($('#calendar') as any).fullCalendar('renderEvents', newEvents, true);
        } catch (e) {} */
    }

    handleDateClick(arg) {
        // console.log('date click! ' + arg.dateStr);
    }

}
