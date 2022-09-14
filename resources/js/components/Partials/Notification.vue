<template>
  <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown"
        class="nav-link notification-toggle nav-link-lg"><i data-feather="bell" class="bell"></i>
        <span id="notCount" v-if="count > 0" class="dot-danger">{{ count }}</span>
    </a>
    <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
        <div class="dropdown-header">
        Notifications
        </div>
        <hr>
        <div class="dropdown-list-content dropdown-list-icons">
            
            <a v-for="(not,i) in notifications" :key="i" href="#" class="dropdown-item"> 
                <template v-if="not.type == 'application_approve'">
                    <span class="dropdown-item-icon bg-success text-white"> <i class="fas fa-check-circle"></i></span> 
                    <span class="dropdown-item-desc">
                        <span class="d-block"><strong>Leave request accepted</strong></span>
                        <span>{{ not.msg }}</span> 
                        <vue-moments-ago class="time" prefix="" suffix="ago" :date="not.created_at" lang="en"></vue-moments-ago>
                    </span>
                </template>
                <template v-else-if="not.type == 'application_rejected'">
                    <span class="dropdown-item-icon bg-danger text-white"> <i class="fas fa-times-circle"></i></span> 
                    <span class="dropdown-item-desc">
                        <span class="d-block"><strong>Leave request rejected</strong></span>
                        <span>{{ not.msg }}</span> 
                        <vue-moments-ago class="time" prefix="" suffix="ago" :date="not.created_at" lang="en"></vue-moments-ago>
                    </span>
                </template>

                <template v-else-if="not.type == 'salary_deposit'">
                    <span class="dropdown-item-icon bg-primary text-white"> <i class="fas fa-dollar-sign"></i></span> 
                    <span class="dropdown-item-desc">
                        <span class="d-block"><strong>Salary Deposited to bank</strong></span>
                        <span>{{ not.msg }}</span> 
                        <vue-moments-ago class="time" prefix="" suffix="ago" :date="not.created_at" lang="en"></vue-moments-ago>
                    </span>
                </template>

                <template v-else-if="not.type == 'new_leave_request'">
                    <span class="dropdown-item-icon bg-warning text-white"> <i class="fas fa-bullhorn"></i></span> 
                    <span class="dropdown-item-desc">
                        <span class="d-block"><strong>New Leave Request</strong></span>
                        <span>{{ not.msg }}</span> 
                        <vue-moments-ago class="time" prefix="" suffix="ago" :date="not.created_at" lang="en"></vue-moments-ago>
                    </span>
                </template>
            </a>

        </div>
        <div class="dropdown-footer text-center">
            <router-link :to="{name: 'notification'}">View All <i class="fas fa-chevron-right"></i></router-link>
        </div>
    </div>
    </li>
</template>

<script>
import VueMomentsAgo from 'vue-moments-ago'

export default {
    components: {
        VueMomentsAgo
    },
    data() {
        return {
            count: 0,
            notifications: [],
        }
    },
    methods: {
        getNotifications() {
            axios.get("/user-notifications?limit=20").then(resp=>{
                return resp.data;
            }).then(data=>{
                if(data.status == "ok") {
                    this.notifications = data.data;
                    this.count = data.unseen;
                }
            }).catch(err=>{
                console.error(err.response.data);
            });
        }
    },
    mounted() {
        this.getNotifications();
    }
}
</script>

<style>

</style>