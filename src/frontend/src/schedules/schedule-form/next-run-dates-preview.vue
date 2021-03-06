<template>
    <div>
        <div class="text-center"
            v-if="value.fetchingNextRunDates">
            <button-loading-dots></button-loading-dots>
        </div>
        <div class="forum-group"
            v-if="value.length > 0"
            :class="{opacity60: value.fetching}">
            <h4>{{ $t('The next available executions') }}</h4>
            <div class="list-group">
                <div class="list-group-item"
                    :key="nextScheduleExecution.plannedTimestamp"
                    v-for="nextScheduleExecution in value">
                    <span class="pull-right small text-muted">
                        <span v-if="schedule.actionId"
                            class="label label-default">
                            {{ $t(nextScheduleExecution.action.caption) }}
                        </span>
                        &nbsp;
                        <span class="label label-default">
                            {{ humanizeNextRunDate(nextScheduleExecution.plannedTimestamp).fromNow }}
                        </span>
                    </span>
                    {{ humanizeNextRunDate(nextScheduleExecution.plannedTimestamp).date }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from "moment";
    import ButtonLoadingDots from "../../common/gui/loaders/button-loading-dots.vue";
    import Vue from "vue";

    export default {
        props: ['value', 'schedule'],
        components: {ButtonLoadingDots},
        computed: {
            nextRunDatesQuery() {
                return {
                    mode: this.schedule.mode,
                    timeExpression: this.schedule.timeExpression,
                    actionId: this.schedule.actionId,
                    dateStart: this.schedule.mode == 'once' ? undefined : this.schedule.dateStart,
                    dateEnd: this.schedule.mode == 'once' ? undefined : this.schedule.dateEnd
                };
            }
        },
        mounted() {
            this.fetchNextScheduleExecutions();
        },
        methods: {
            fetchNextScheduleExecutions() {
                const query = this.nextRunDatesQuery;
                if (!query.timeExpression) {
                    this.$emit('input', []);
                } else {
                    this.$set(this.value, 'fetching', true);
                    Vue.http.post('schedules/next-schedule-executions', query)
                        .then(({body: nextScheduleExecutions}) => {
                            this.$emit('input', nextScheduleExecutions);
                            if (query != this.nextRunDatesQuery) {
                                this.fetchNextScheduleExecutions();
                            }
                        })
                        .catch(() => this.$emit('input', []));
                }
            },
            humanizeNextRunDate(dateString) {
                return {
                    date: moment(dateString).format('LLL'),
                    fromNow: moment(dateString).fromNow()
                };
            },
        },
        watch: {
            'schedule.timeExpression'() {
                this.fetchNextScheduleExecutions();
            },
            'schedule.mode'() {
                this.fetchNextScheduleExecutions();
            },
            'schedule.dateStart'() {
                this.fetchNextScheduleExecutions();
            },
            'schedule.dateEnd'() {
                this.fetchNextScheduleExecutions();
            },
            'schedule.actionId'() {
                this.fetchNextScheduleExecutions();
            }
        }
    };
</script>

<style>
    .opacity60 {
        opacity: .6;
    }
</style>
