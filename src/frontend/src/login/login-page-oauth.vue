<template>
    <form @submit="authenticating = true"
        :action="askForTargetCloud ? '/oauth/v2/broker_login' : '/oauth/v2/auth_login'"
        ref="loginForm"
        method="post"
        v-title="$t('Login')"
        :class="'centered-form-container login-oauth-form ' + (askForTargetCloud ? 'login-oauth-form-broker' : '')">
        <login-form :authenticating="authenticating"
            :oauth="true"
            :error="error"
            :intitial-username="lastUsername"
            :submit-button-text="askForTargetCloud ? $t('Proceed') : ''">
            <template slot="aboveForm">
                <h4 v-if="clientName"
                    class="client-name-prompt">
                    {{ $t('{clientName} wants to access your account.', {clientName: clientName}) }}
                </h4>
            </template>
            <div v-if="askForTargetCloud">
                <div class="form-group text-center">
                    <label>
                        <div class="checkbox checkbox-green">
                            <label>
                                <input type="checkbox"
                                    v-model="ownCloud"
                                    @change="error = undefined">
                                <span class="checkmark"></span>
                                {{ $t('Connection with a private instance of the SUPLA cloud') }}
                            </label>
                        </div>
                    </label>
                </div>
                <transition-expand>
                    <div class="form-group form-group-lg"
                        v-if="ownCloud">
                        <span class="input-group">
                            <span class="input-group-addon">
                                <span class="pe-7s-global"></span>
                            </span>
                            <input type="text"
                                required
                                autocorrect="off"
                                autocapitalize="none"
                                :placeholder="$t('Private Cloud domain name')"
                                v-model="targetCloud"
                                name="targetCloud"
                                class="form-control">
                        </span>
                        <span class="help-block">
                            {{ $t('Only domain names with an optional port number are allowed. E.g. mysupla.org or mysupla.org:88. HTTPS is required.') }}
                        </span>
                    </div>
                </transition-expand>
                <transition name="fade">
                    <div class="error"
                        v-if="error">
                        <div v-if="error == 'autodiscover_fail'">
                            <div v-if="ownCloud">
                                <strong>{{ $t('We could not connect to your SUPLA Cloud instance.') }}</strong>
                                {{ $t('Your instance is not registered or you are trying to authorize an application that is not public.') }}
                            </div>
                            <div v-else>
                                <strong>{{ $t('We were not able to find your account.') }}</strong>
                                {{ $t('If you are sure you have an account on cloud.supla.org, check if the application you are trying to authorize is public.') }}
                            </div>
                        </div>
                        <div v-if="error == 'private_cloud_fail'">
                            {{ $t('Your private SUPLA Cloud instance is not available. Make sure your server is online and your https connection works properly.') }}
                        </div>
                    </div>
                </transition>
            </div>
        </login-form>
    </form>
</template>

<script>
    import LoginForm from "./login-form";
    import TransitionExpand from "../common/gui/transition-expand";

    export default {
        props: ['lastUsername', 'error', 'askForTargetCloud', 'lastTargetCloud', 'clientName'],
        components: {TransitionExpand, LoginForm},
        data() {
            return {
                ownCloud: false,
                authenticating: false,
                displayError: false,
                targetCloud: '',
            };
        },
        mounted() {
            if (this.lastTargetCloud) {
                this.ownCloud = true;
                this.targetCloud = this.lastTargetCloud;
            }
        }
    };
</script>

<style lang="scss">
    @import "../styles/variables";

    .login-oauth-form-broker {
        .login-password {
            display: none;
        }
    }

    .client-name-prompt {
        text-align: center;
        margin-bottom: 25px;
    }
</style>
