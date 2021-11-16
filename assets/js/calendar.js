import { Calendar, diffDates } from '@fullcalendar/core';
import adaptivePlugin from '@fullcalendar/adaptive';
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import listPlugin from '@fullcalendar/list';
import timeGridPlugin from '@fullcalendar/timegrid';
import resourceTimelinePlugin from '@fullcalendar/resource-timeline';
import esLocale from '@fullcalendar/core/locales/es';
import frLocale from '@fullcalendar/core/locales/fr';

document.addEventListener('DOMContentLoaded', function() {
    var today = new Date();
    var today = today.getFullYear() + "-" + String(today.getMonth() + 1).padStart(2, '0') + "-" +  String(today.getDate()).padStart(2, '0');

    let calendarEl = document.getElementById('calendar');
    var calendar = new Calendar(calendarEl, {
        locales: [ esLocale, frLocale ],
        locale: 'fr' ,
        plugins: [ adaptivePlugin, interactionPlugin, dayGridPlugin, listPlugin, timeGridPlugin, resourceTimelinePlugin ],
        schedulerLicenseKey: 'XXX',
        now: today,
        editable: true, // enable draggable events
        aspectRatio: 1.8,
        scrollTime: '00:00', // undo default 6am scrollTime
        headerToolbar: {
        left: 'today prev,next',
        center: 'title',
        right: 'resourceTimelineDay,timeGridWeek,dayGridMonth,listWeek'
        },
        initialView: 'dayGridMonth',
        events: 
        {
          url: '/fullCalendarEvents',
          method: 'POST',
          failure: function(e) {
            console.log(e)
          },
          textColor: '#fff' // a non-ajax option
        },
        resources: 
        {
          url: '/fullCalendarRessources',
          method: 'POST'
        },
        eventClick: function(info) {
          console.log(info.event);
        },
        eventContent: function(event) {
          let div = document.createElement('div')
          let divActions = document.createElement('div')
          let eventDiv = $.find("[data-date='"+event.event.startStr.substring(0,10)+"']");
          $(eventDiv).append(div);
          // let eventChild = $(eventDiv[0]).children().children()[1];

          if(event.event.extendedProps.holiday){
            $(eventDiv).css( "background-color", "#E7E7E7" );
            let eventChild = $(eventDiv[0]).children().children()[1];
            $(eventChild).css( "visibility", "hidden" );
          }
          else{
            
            $( div ).addClass("eventFullcalendar d-flex flex-wrap");
            if(event.event.extendedProps.status == 0){
              $( div ).append( "<span class='p-1 text-white'><s>"+event.event.title+"</s></span>" );
              $(div).css( "background-color", "#DC4C4C" );

            }
            else{
              $( div ).append( "<span class='p-1 text-white'>"+event.event.title+"</span>" );
              $(div).css( "background-color", event.event.backgroundColor );
            }

            $( divActions ).append( "<a href='/plannings/del/"+event.event.id+"' class='fullCalendarActionButton'><i class='mdi mdi-trash-can-outline'></i></a>" );
            $( divActions ).append( "<a href='/plannings/edit/"+event.event.id+"' class='fullCalendarActionButton pr-1'><i class='mdi mdi-pencil-box-outline'></i></a> </div>" );

            $( div ).append(divActions)
            let arrayOfDomNodes = [ div ]
            return { domNodes: arrayOfDomNodes }
          }
          
        },
        eventDrop: function(info) {
          $.ajax({
            method: "POST",
            url: "/plannings/edit/"+info.event.id,
            data: {id: info.event.id, title: info.event.title, start: info.event.start.toISOString().substring(0,10), action: 'drop'}
          })
          .done(function(result) {
            calendar.refetchEvents()
          })
          .fail(function(e) {
            console.log(e);
          })
        },
        progressiveEventRendering: true,

    });

    calendar.render();
});