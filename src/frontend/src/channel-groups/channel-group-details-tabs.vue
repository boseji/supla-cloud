<template>
    <div>
        <div class="container"
            v-if="availableTabs.length">
            <div class="form-group">
                <ul class="nav nav-tabs">
                    <li :class="currentTab == tabDefinition.id ? 'active' : ''"
                        :key="tabDefinition.id"
                        v-for="tabDefinition in availableTabs">
                        <a @click="changeTab(tabDefinition.id)">
                            {{ $t(tabDefinition.header) }}
                            <span v-if="tabDefinition.count !== undefined">({{ tabDefinition.count }})</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div v-if="currentTab == 'actions'">
            <div class="container">
                <channel-action-executor :subject="channelGroup"></channel-action-executor>
            </div>
        </div>
        <div v-if="currentTab == 'schedules'">
            <schedule-list-page :subject="channelGroup"></schedule-list-page>
        </div>
        <div v-if="currentTab == 'directLinks'">
            <direct-links-list :subject="channelGroup"></direct-links-list>
        </div>
    </div>
</template>

<script>
    import ScheduleListPage from "../schedules/schedule-list/schedule-list-page";
    import DirectLinksList from "../direct-links/direct-links-list";
    import ChannelActionExecutor from "../channels/action/channel-action-executor";

    export default {
        props: ['channelGroup'],
        components: {ChannelActionExecutor, DirectLinksList, ScheduleListPage},
        data() {
            return {
                currentTab: '',
                availableTabs: []
            };
        },
        methods: {
            changeTab(id) {
                this.currentTab = id;
                this.$router.push({query: {tab: id}});
            }
        },
        mounted() {
            const noApiActionFunctions = ['VALVEOPENCLOSE', 'VALVEPERCENTAGE'];
            if (!noApiActionFunctions.includes(this.channelGroup.function.name)) {
                this.availableTabs.push({id: 'actions', header: 'Actions'});
            }
            const noScheduleActions = ['CONTROLLINGTHEGATE', 'CONTROLLINGTHEGARAGEDOOR'];
            if (!noScheduleActions.includes(this.channelGroup.function.name)) {
                this.availableTabs.push({id: 'schedules', header: 'Schedules', count: this.channelGroup.relationsCount.schedules});
            }
            this.availableTabs.push({id: 'directLinks', header: 'Direct links', count: this.channelGroup.relationsCount.directLinks});
            const currentTab = this.availableTabs.filter(tab => tab.id == this.$route.query.tab)[0];
            this.currentTab = currentTab ? currentTab.id : this.availableTabs[0].id;
        },
    };
</script>
