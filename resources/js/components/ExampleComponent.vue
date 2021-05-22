<template>
    <div class="row">
        <div class="col-5">
            <h1>Event</h1>
            <form @submit.prevent="saveEvents" class="form">
                <div class="form-group">
                    <label for="eventName">Name</label>
                    <input id="eventName" type="text" class="form-control" 
                            v-model="name" />
                </div>
                <div class="form-group row">
                    <div class="col-6">
                        <label for="dateFrom">From:</label>
                        <datepicker
                            id="dateFrom"
                            :bootstrap-styling="true"
                            v-model="from"
                            :disabled-dates="disabledDatesFrom"
                            @selected="updateDateFrom"
                        ></datepicker>
                    </div>
                    <div class="col-6">
                        <label for="dateTo">To:</label>
                        <datepicker
                            id="dateTo"
                            :bootstrap-styling="true"
                            v-model="to"
                            :disabled-dates="disabledDatesTo"
                            @selected="updateDateTo"
                        ></datepicker>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Select Days:</label><br />
                    <label class="form-check form-check-inline">
                        <input
                            type="checkbox"
                            v-model="checkDays"
                            id="inlineCheckbox1"
                            class="form-check-input"
                            value="1"
                        />
                        Mon
                    </label>
                    <label class="form-check form-check-inline">
                        <input
                            type="checkbox"
                            v-model="checkDays"
                            id="inlineCheckbox2"
                            class="form-check-input"
                            value="2"
                        />
                        Tue
                    </label>
                    <label class="form-check form-check-inline">
                        <input
                            type="checkbox"
                            v-model="checkDays"
                            id="inlineCheckbox3"
                            class="form-check-input"
                            value="3"
                        />
                        Wed
                    </label>
                    <label class="form-check form-check-inline">
                        <input
                            type="checkbox"
                            v-model="checkDays"
                            id="inlineCheckbox4"
                            class="form-check-input"
                            value="4"
                        />
                        Thu
                    </label>
                    <label class="form-check form-check-inline">
                        <input
                            type="checkbox"
                            v-model="checkDays"
                            id="inlineCheckbox5"
                            class="form-check-input"
                            value="5"
                        />
                        Fri
                    </label>
                    <label class="form-check form-check-inline">
                        <input
                            type="checkbox"
                            v-model="checkDays"
                            id="inlineCheckbox6"
                            class="form-check-input"
                            value="6"
                        />
                        Sat
                    </label>
                    <label class="form-check form-check-inline">
                        <input
                            type="checkbox"
                            v-model="checkDays"
                            id="inlineCheckbox7"
                            class="form-check-input"
                            value="0"
                        />
                        Sun
                    </label>
                    <div class="clearfix"></div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <font-awesome-icon icon="save" /> Save
                </button>
            </form>
        </div>
        <div class="col-7">
            <div>
                <table class="table">
                    <tr>
                        <td>
                            <button
                                @click="lastMonth"
                                type="button"
                                class="btn btn-primary"
                            >
                                <font-awesome-icon icon="backward" />
                            </button>
                        </td>
                        <td width="80%" align="center">
                            <h1>{{ month }} {{ year }}</h1>
                        </td>
                        <td>
                            <button
                                @click="nextMonth"
                                type="button"
                                class="btn btn-primary"
                            >
                                <font-awesome-icon icon="forward" />
                            </button>
                        </td>
                    </tr>
                </table>

                <table class="dates month table">
                    <!-- <tr v-for="date in daysInMonth" v-bind:key="date"  v-bind:data-day=date>
                        <td align="left">{{ date | weekday(month, year) }}</td>
                        <td>{{ date }}</td>
                        <td width="70%"><p></p></td>
                    </tr> -->
                    <tr v-for="event in events" v-bind:key="event.day" :class="event.name ? 'table-success' : ''">
                        <td align="left">{{ event.day | weekday(month, year) }}</td>
                        <td>{{ event.day }}</td>
                        <td width="70%">{{ event.name }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import moment from "moment";
import Datepicker from "vuejs-datepicker";
import Vue from 'vue';
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
Vue.use(VueToast);

export default {
    components: { Datepicker },
    data() {
        return {
            events: [],
            name: "",
            checkDays: [],
            dateContext: moment(),
            //date picker
            from: new Date(),
            to: new Date(),
            disabledDatesFrom: {
                from: new Date()
            },
            disabledDatesTo: {
                to: new Date()
            }
        };
    },
    methods: {
        nextMonth: function() {
            var t = this;
            t.dateContext = moment(t.dateContext).add(1, "month");
            this.getEvents();
        },
        lastMonth: function() {
            var t = this;
            t.dateContext = moment(t.dateContext).subtract(1, "month");
            this.getEvents();
        },
        updateDateFrom: function(selected) {
            this.disabledDatesTo.to = selected;
            if (this.to < this.disabledDatesTo.to) {
                this.to = this.disabledDatesTo.to;
            }
        },
        updateDateTo: function(selected) {
            this.disabledDatesFrom.from = selected;
            if (this.from < this.disabledDatesTo.from) {
                this.from = this.disabledDatesTo.from;
            }
        },
        saveEvents: function(e) {
            e.preventDefault();
            this.loading = true;
            let currentObj = this;
            axios
                .post("/api/save-events", {
                    name: this.name,
                    dateFrom: moment(this.from).format('DD-MM-YYYY'),
                    dateTo: moment(this.to).format('DD-MM-YYYY'),
                    checkDays: this.checkDays
                })
                .then((response) => {
                    console.log(response);
                    currentObj.output = response.data;
                    this.loading.false;
                    Vue.$toast.success('Events saved.', {
                        position: 'top-right'
                    })
                    this.getEvents();
                })
                .catch((error) => {
                    currentObj.output = error;
                });
        },
        getEvents() {
            this.loading = true;
            this.events = [];
            var t = this;
            var currentDate =  moment(t.dateContext);
            var dateTo = moment(currentDate).endOf('month').format('YYYY-MM-DD');
            var dateFrom = moment(currentDate).startOf('month').format('YYYY-MM-DD');
            axios
                .get("/api/get-events", {
                    params: {
                      dateTo: dateTo,
                      dateFrom: dateFrom
                    }
                })
                .then(response =>  {
                    var index = 1;
                    response.data.forEach(element => {
                        this.events.push({"day": index++, "name": (element != null) ? element.name : ""});
                    });
                    this.loading = false;
                })
                .catch((error) =>  {
                    console.log(error);
                });
        },
    },
    computed: {
        year: function() {
            var t = this;
            return t.dateContext.format("YYYY");
        },
        month: function() {
            var t = this;
            return t.dateContext.format("MMMM");
        },
        daysInMonth: function() {
            var t = this;
            return t.dateContext.daysInMonth();
        },
        currentDate: function() {
            var t = this;
            return t.dateContext.get("date");
        }
    },
    filters: {
        weekday: function(date, month, year) {
            var currentDate = moment()
                .date(date)
                .year(year)
                .month(month);
            return moment(currentDate).format("dddd");
        }
    },
    created() {
        this.getEvents();
    },
};
</script>
