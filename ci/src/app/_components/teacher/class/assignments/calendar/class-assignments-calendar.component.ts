import {Component, OnInit, Input} from '@angular/core';
import {CalendarOptions} from '@fullcalendar/angular';
import * as $ from 'jquery';

@Component({
    selector: 'app-class-assignments-calendar',
    templateUrl: './class-assignments-calendar.component.html',
    styleUrls: ['./class-assignments-calendar.component.scss'],
    providers: []
})
export class ClassAssignmentsCalendarComponent implements OnInit {

    @Input() assignments;
    currentDate = (new Date()).toISOString().split('T')[0];

    calendarOptions: CalendarOptions = {
        initialView: 'dayGridMonth',
        dateClick: this.handleDateClick.bind(this), // bind is important!
        events: []
    };

    constructor() {}

    ngOnInit() {
        this.updateCalendarEvents();
    }

    updateCalendarEvents() {
        try {
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
            ($('#calendar') as any).fullCalendar('renderEvents', newEvents, true);
        } catch (e) {}
    }

    handleDateClick(arg) {
        // console.log('date click! ' + arg.dateStr);
    }

}
