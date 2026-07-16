<x-layouts.app title="SprintMind Ai - الجدول الزمني التفاعلي">

    <!-- CSRF Token and FullCalendar CDN -->
    <script>
        window.csrfToken = '{{ csrf_token() }}';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <style>
        .fc {
            font-family: 'Tajawal', sans-serif;
        }

        .fc .fc-button-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
            font-weight: 700;
            font-size: 0.75rem;
            border-radius: 0.75rem;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
            text-transform: capitalize;
        }

        .fc .fc-button-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
            opacity: 0.95;
        }

        .fc .fc-button-primary:disabled {
            background-color: #cbd5e1;
            border-color: #cbd5e1;
            color: #475569;
        }

        .fc .fc-button-active {
            background-color: #312e81 !important;
            border-color: #312e81 !important;
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #f1f5f9 !important;
        }

        .fc-theme-standard .fc-scrollgrid {
            border-color: #e2e8f0 !important;
            border-radius: 1.25rem;
            overflow: hidden;
        }

        .fc-daygrid-day-frame {
            min-height: 110px;
            transition: background-color 0.2s;
        }

        .fc-daygrid-day-number {
            font-weight: 700;
            font-family: 'Geist', sans-serif;
            color: #0f172a;
            font-size: 0.85rem;
        }

        .fc-col-header-cell-cushion {
            font-weight: 800;
            color: #475569;
            font-size: 0.85rem;
            padding: 8px 0 !important;
        }

        .fc-event {
            background: transparent !important;
            border: none !important;
            padding: 2px 4px !important;
        }

        /* Scroller customize */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
    </style>

    <div x-data="agileCalendar()" class="flex flex-col lg:flex-row gap-6">

        <!-- Left Column: Main Calendar Area -->
        <div class="flex-1 bg-white p-6 rounded-3xl border border-outline-variant/60 card-elevation">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="bg-primary-container text-on-primary-container text-[11px] font-extrabold px-2.5 py-0.5 rounded-full">الجدول الزمني التفاعلي</span>
                        <span class="text-xs text-on-surface-variant">📅 تحكم مرئي كامل بالمهام</span>
                    </div>
                    <h2 class="text-2xl font-black text-on-surface">مخطط سبرنتات ومواعيد الاستحقاق</h2>
                </div>

                <!-- AI Auto-Scheduler Form -->
                <div class="flex items-center gap-3 bg-surface-container/50 p-2 rounded-2xl border border-outline-variant/40">
                    <div class="flex flex-col">
                        <label class="text-[10px] font-bold text-on-surface-variant mb-0.5">تاريخ بدء الجدولة</label>
                        <input type="date" x-model="aiStartDate" class="bg-white border border-outline-variant/60 rounded-xl text-xs px-2.5 py-1.5 text-on-surface font-bold focus:ring-primary focus:border-primary">
                    </div>
                    <button @click="runAutoSchedule()" :disabled="isLoadingAI" class="bg-gradient-to-r from-primary to-indigo-600 text-white font-bold px-4 py-3.5 rounded-xl text-xs hover:opacity-95 shadow-md flex items-center gap-2 transition-all mt-4.5">
                        <span x-show="isLoadingAI" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        <span x-show="!isLoadingAI" class="material-symbols-outlined text-[16px] text-emerald-300">auto_awesome</span>
                        <span x-text="isLoadingAI ? 'جاري الجدولة...' : '⚡ جدولة ذكية بالـ AI'"></span>
                    </button>
                </div>
            </div>

            <!-- Heatmap color guide -->
            <div class="flex items-center gap-4 text-xs font-bold mb-4 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                <span class="text-on-surface-variant">مؤشر سعة اليوم (Daily Capacity):</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-emerald-100 border border-emerald-300"></span> مثالي (<= 5)</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-amber-100 border border-amber-300"></span> شبه ممتلئ (6-8)</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-rose-100 border border-rose-300"></span> محمل بكثرة (> 8)</span>
            </div>

            <!-- FullCalendar placeholder -->
            <div id="calendar" class="w-full"></div>
        </div>

        <!-- Right Column: Backlog Drawer -->
        <div class="w-full lg:w-80 bg-white p-5 rounded-3xl border border-outline-variant/60 card-elevation flex flex-col h-[calc(100vh-140px)] sticky top-6">
            <div class="mb-4">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-primary text-[20px]">inventory_2</span>
                    <h3 class="text-base font-black text-on-surface">درج المهام غير المجدولة</h3>
                </div>
                <p class="text-xs text-on-surface-variant leading-relaxed">اسحب المهمة من هنا وأسقطها على اليوم المحدد بالجدول لجدولتها فوراً.</p>
            </div>

            <!-- Draggable elements container -->
            <div id="external-events" class="flex-1 overflow-y-auto custom-scrollbar pr-1">
                <template x-for="task in backlogTasks" :key="task.id">
                    <div
                        :class="getBacklogClass(task.priority)"
                        class="fc-event-backlog p-3.5 rounded-2xl border mb-3 cursor-grab hover:shadow-md transition-all active:cursor-grabbing text-slate-800"
                        :data-task="JSON.stringify(task)">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[9px] font-extrabold text-slate-400 truncate max-w-[120px]" x-text="task.project ? task.project.name : 'بدون مشروع'"></span>
                            <span :class="getDotClass(task.priority)" class="h-2 w-2 rounded-full"></span>
                        </div>
                        <div class="text-xs font-black text-slate-800 mb-2 leading-tight" x-text="task.title"></div>
                        <div class="flex items-center justify-between text-[10px] font-bold">
                            <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full">⚡ <span x-text="task.story_points"></span>pt</span>
                            <span class="text-slate-400 text-[9px]" x-text="task.priority === 'high' ? 'عالية' : (task.priority === 'low' ? 'منخفضة' : 'متوسطة')"></span>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <div x-show="backlogTasks.length === 0" class="text-center py-12 px-4 text-slate-400">
                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">task_alt</span>
                    <p class="text-xs font-bold">الدرج فارغ!</p>
                    <p class="text-[10px] text-slate-400 mt-1">تمت جدولة جميع المهام أو لا توجد مهام معلقة.</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine component initialization -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('agileCalendar', () => ({
                backlogTasks: [],
                isLoadingAI: false,
                aiStartDate: new Date().toLocaleDateString('en-CA'),
                calendar: null,

                init() {
                    this.fetchUnscheduled();
                    this.initCalendar();
                },

                fetchUnscheduled() {
                    fetch('/calendar/unscheduled')
                        .then(res => res.json())
                        .then(data => {
                            this.backlogTasks = data;
                            this.$nextTick(() => {
                                this.initDraggable();
                            });
                        })
                        .catch(err => console.error('Error fetching backlog:', err));
                },

                initDraggable() {
                    const containerEl = document.getElementById('external-events');
                    if (!containerEl) return;

                    // Clean previous draggable instances if any
                    if (window.fcDraggable) {
                        window.fcDraggable.destroy();
                    }

                    window.fcDraggable = new FullCalendar.Draggable(containerEl, {
                        itemSelector: '.fc-event-backlog',
                        eventData: function(eventEl) {
                            const task = JSON.parse(eventEl.getAttribute('data-task'));
                            return {
                                id: task.id,
                                title: task.title,
                                duration: {
                                    days: 1
                                },
                                allDay: true,
                                extendedProps: {
                                    priority: task.priority,
                                    story_points: task.story_points,
                                    status: task.status,
                                    is_ai_generated: task.is_ai_generated,
                                    project_name: task.project ? task.project.name : 'بدون مشروع'
                                }
                            };
                        }
                    });
                },

                initCalendar() {
                    const calendarEl = document.getElementById('calendar');
                    if (!calendarEl) return;

                    const self = this;
                    this.calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        locale: 'ar',
                        direction: 'rtl',
                        headerToolbar: {
                            right: 'prev,next today',
                            center: 'title',
                            left: 'dayGridMonth,timeGridWeek,listWeek'
                        },
                        buttonText: {
                            today: 'اليوم',
                            month: 'شهر',
                            week: 'أسبوع',
                            list: 'قائمة الجدولة'
                        },
                        editable: true,
                        droppable: true,
                        events: '/calendar/events',
                        eventContent: function(arg) {
                            const priority = arg.event.extendedProps.priority || 'medium';
                            const points = arg.event.extendedProps.story_points || 0;
                            const project = arg.event.extendedProps.project_name || 'بدون مشروع';
                            const status = arg.event.extendedProps.status || 'pending';
                            const isAi = arg.event.extendedProps.is_ai_generated;

                            let dotColor = 'bg-amber-500';
                            if (priority === 'high') dotColor = 'bg-rose-500';
                            if (priority === 'low') dotColor = 'bg-sky-500';

                            let statusLabel = 'قيد الانتظار';
                            if (status === 'in_progress') statusLabel = 'قيد التنفيذ';
                            if (status === 'completed') statusLabel = 'مكتمل';

                            let borderLeftColor = 'border-l-4 border-l-amber-500';
                            if (priority === 'high') borderLeftColor = 'border-l-4 border-l-rose-500';
                            if (priority === 'low') borderLeftColor = 'border-l-4 border-l-sky-500';

                            let html = `
                                <div class="flex flex-col w-full p-2 bg-white rounded-xl shadow-sm border border-slate-100 ${borderLeftColor} hover:shadow-md transition-all cursor-grab active:cursor-grabbing text-slate-800">
                                    <div class="flex items-center justify-between gap-1 mb-1">
                                        <span class="text-[9px] text-slate-400 font-bold max-w-[80px] truncate" title="${project}">${project}</span>
                                        <span class="flex h-1.5 w-1.5 shrink-0 rounded-full ${dotColor}"></span>
                                    </div>
                                    <div class="text-[11px] font-bold mb-1 text-slate-800 line-clamp-2 leading-tight">${arg.event.title}</div>
                                    <div class="flex items-center justify-between mt-auto pt-1 border-t border-slate-50 gap-1.5">
                                        <div class="flex items-center gap-1">
                                            <span class="bg-indigo-50 text-indigo-700 text-[8px] font-extrabold px-1.5 py-0.5 rounded-full flex items-center gap-0.5">
                                                <span>⚡</span><span>${points}pt</span>
                                            </span>
                                            ${isAi ? `
                                            <span class="bg-violet-50 text-violet-700 text-[8px] font-black px-1 py-0.5 rounded-full border border-violet-100" title="تم إنشاؤه بالذكاء الاصطناعي">
                                                AI
                                            </span>` : ''}
                                        </div>
                                        <span class="text-[8px] font-bold text-slate-400">${statusLabel}</span>
                                    </div>
                                </div>
                            `;
                            return {
                                html: html
                            };
                        },

                        // Drag/drop from external backlog
                        eventReceive: function(info) {
                            self.handleReschedule(info);
                        },

                        // Drag/drop internally
                        eventDrop: function(info) {
                            self.handleReschedule(info);
                        },

                        // Resizing internally
                        eventResize: function(info) {
                            self.handleReschedule(info);
                        },

                        eventsSet: function() {
                            setTimeout(() => {
                                self.updateDailyCapacity();
                            }, 50);
                        },

                        datesSet: function() {
                            setTimeout(() => {
                                self.updateDailyCapacity();
                            }, 50);
                        }
                    });

                    this.calendar.render();
                },

                getEventDates(event) {
                    let startDate = event.startStr ? event.startStr.split('T')[0] : event.start.toLocaleDateString('en-CA');
                    let endDate = null;

                    if (event.end) {
                        let date = new Date(event.end);
                        date.setDate(date.getDate() - 1);
                        endDate = date.toLocaleDateString('en-CA');
                    } else {
                        endDate = startDate;
                    }

                    return {
                        start_date: startDate,
                        due_date: endDate
                    };
                },

                handleReschedule(info) {
                    const eventDates = this.getEventDates(info.event);
                    fetch(`/calendar/reschedule/${info.event.id}`, {
                            method: 'POST', // Use POST to ensure reliable transmission
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': window.csrfToken
                            },
                            body: JSON.stringify({
                                _method: 'PATCH', // Laravel Method Spoofing
                                start_date: eventDates.start_date,
                                due_date: eventDates.due_date
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.backlogTasks = this.backlogTasks.filter(t => t.id != info.event.id);
                                this.updateDailyCapacity();
                            } else {
                                alert(data.error || 'فشلت عملية إعادة الجدولة.');
                                if (info.revert) {
                                    info.revert();
                                } else {
                                    info.event.remove();
                                }
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            if (info.revert) {
                                info.revert();
                            } else {
                                info.event.remove();
                            }
                        });
                },

                updateDailyCapacity() {
                    if (!this.calendar) return;

                    const events = this.calendar.getEvents();
                    const dailyPoints = {};

                    events.forEach(event => {
                        const points = parseInt(event.extendedProps.story_points || 0);
                        if (points === 0) return;

                        const startStr = event.startStr ? event.startStr.split('T')[0] : event.start.toLocaleDateString('en-CA');
                        const endStr = event.endStr ? event.endStr.split('T')[0] : (event.end ? event.end.toLocaleDateString('en-CA') : startStr);

                        let current = new Date(startStr + 'T00:00:00');
                        let end = new Date(endStr + 'T00:00:00');

                        while (current < end || (event.end === null && current.getTime() === new Date(startStr + 'T00:00:00').getTime())) {
                            const dateStr = current.toLocaleDateString('en-CA');
                            dailyPoints[dateStr] = (dailyPoints[dateStr] || 0) + points;

                            if (event.end === null) break;
                            current.setDate(current.getDate() + 1);
                        }
                    });

                    // Reset all cells
                    document.querySelectorAll('.fc-day').forEach(el => {
                        el.classList.remove('bg-emerald-50/60', 'bg-amber-50/60', 'bg-rose-50/60');
                        const badge = el.querySelector('.daily-capacity-badge');
                        if (badge) badge.remove();
                    });

                    // Set capacity styles
                    for (const [dateStr, points] of Object.entries(dailyPoints)) {
                        const cell = document.querySelector(`.fc-day[data-date="${dateStr}"]`);
                        if (cell) {
                            if (points <= 5) {
                                cell.classList.add('bg-emerald-50/60');
                            } else if (points <= 8) {
                                cell.classList.add('bg-amber-50/60');
                            } else {
                                cell.classList.add('bg-rose-50/60');
                            }

                            const topEl = cell.querySelector('.fc-daygrid-day-top');
                            if (topEl) {
                                const badge = document.createElement('div');
                                badge.className = 'daily-capacity-badge text-[9px] font-extrabold px-1.5 py-0.5 rounded-full mr-auto flex items-center gap-0.5 shadow-sm ';
                                if (points <= 5) {
                                    badge.className += 'bg-emerald-100 text-emerald-800 border border-emerald-300';
                                    badge.innerHTML = `🟢 ${points}pt`;
                                } else if (points <= 8) {
                                    badge.className += 'bg-amber-100 text-amber-800 border border-amber-300';
                                    badge.innerHTML = `🟡 ${points}pt`;
                                } else {
                                    badge.className += 'bg-rose-100 text-rose-800 border border-rose-300 animate-pulse';
                                    badge.innerHTML = `🔴 ${points}pt 🔥`;
                                }
                                topEl.prepend(badge);
                            }
                        }
                    }
                },

                runAutoSchedule() {
                    this.isLoadingAI = true;
                    fetch('/calendar/auto-schedule', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': window.csrfToken
                            },
                            body: JSON.stringify({
                                start_date: this.aiStartDate
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            this.isLoadingAI = false;
                            if (data.success) {
                                if (data.count > 0) {
                                    this.calendar.refetchEvents();
                                    this.fetchUnscheduled();
                                    alert(`تمت الجدولة بنجاح! تم توزيع ${data.count} مهام بنجاح.`);
                                } else {
                                    alert(data.message || 'لا توجد مهام إضافية لجدولتها.');
                                }
                            } else {
                                alert(`خطأ: ${data.error || 'فشلت عملية الجدولة التلقائية.'}`);
                            }
                        })
                        .catch(err => {
                            this.isLoadingAI = false;
                            console.error(err);
                            alert('فشلت عملية الجدولة التلقائية بسبب خطأ في الشبكة.');
                        });
                },

                getBacklogClass(priority) {
                    if (priority === 'high') return 'border-l-4 border-l-rose-500 bg-rose-50/20 hover:bg-rose-50/40 border-slate-200';
                    if (priority === 'low') return 'border-l-4 border-l-sky-500 bg-sky-50/20 hover:bg-sky-50/40 border-slate-200';
                    return 'border-l-4 border-l-amber-500 bg-amber-50/20 hover:bg-amber-50/40 border-slate-200';
                },

                getDotClass(priority) {
                    if (priority === 'high') return 'bg-rose-500';
                    if (priority === 'low') return 'bg-sky-500';
                    return 'bg-amber-500';
                }
            }));
        });
    </script>

</x-layouts.app>