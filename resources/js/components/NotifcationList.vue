<template>
  <div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>My notifications</h4>
            </div>
            <div class="card-body">
                <div class="row" v-if="isLoading">
                    <div class="col-12 mb-4" v-for="n in 10" :key="n">
                        <skeleton width="100%" height="40px" />
                    </div>
                </div>
                <div class="row" v-else>
                    <div class="col-12">
                        <table class="table table-hover">
                            <tr class="border-bottom"></tr>
                            <tr v-for="(not,i) in notifications" :key="i" class="border-bottom">
                                <template v-if="not.type=='application_approve'">
                                    <td><span class="icon-40 bg-success text-white"> <i class="fas fa-check-circle"></i></span> </td>
                                    <td>
                                        <p class="m-0"><b>Leave request accepted</b></p>
                                        <p class="m-0">{{ not.msg }}</p>
                                    </td>
                                    <td><vue-moments-ago class="time" prefix="" suffix="ago" :date="not.created_at" lang="en"></vue-moments-ago></td>
                                </template>

                                <template v-if="not.type=='application_rejected'">
                                    <td><span class="icon-40 bg-danger text-white"> <i class="fas fa-times-circle"></i></span> </td>
                                    <td>
                                        <p class="m-0"><b>Leave request rejected</b></p>
                                        <p class="m-0">{{ not.msg }}</p>
                                    </td>
                                    <td><vue-moments-ago class="time" prefix="" suffix="ago" :date="not.created_at" lang="en"></vue-moments-ago></td>
                                </template>

                                <template v-if="not.type=='salary_deposit'">
                                    <td><span class="icon-40 bg-primary text-white"> <i class="fas fa-dollar-sign"></i></span> </td>
                                    <td>
                                        <p class="m-0"><b>Salary Deposited to bank</b></p>
                                        <p class="m-0">{{ not.msg }}</p>
                                    </td>
                                    <td><vue-moments-ago class="time" prefix="" suffix="ago" :date="not.created_at" lang="en"></vue-moments-ago></td>
                                </template>

                                <template v-if="not.type=='new_leave_request'">
                                    <td><span class="icon-40 bg-warning text-white"> <i class="fas fa-bullhorn"></i></span> </td>
                                    <td>
                                        <p class="m-0"><b>New Leave Request</b></p>
                                        <p class="m-0">{{ not.msg }}</p>
                                    </td>
                                    <td><vue-moments-ago class="time" prefix="" suffix="ago" :date="not.created_at" lang="en"></vue-moments-ago></td>
                                </template>

                            </tr>
                        </table>
                    </div>
                    <div class="col-12 d-flex justify-content-center mt-2">
                        <pagination :data="paginateData" @pagination-change-page="getNotifications"></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script>
import VueMomentsAgo from 'vue-moments-ago'
export default {
    components: {
        VueMomentsAgo
    },
    data() {
        return {
            isLoading: true,
            notifications: [],
            paginateData: {},
        }
    },
    methods: {
        getNotifications(page = 1) {
            axios.get("/user-notifications?page="+page).then(resp=>{
                return resp.data;
            }).then(data=>{
                if(data.status == "ok") {
                    this.notifications = data.data.data;
                    this.paginateData = data.data;
                    this.isLoading = false;
                }
            }).catch(err=>{
                console.error(err.response.data);
            });
        }
    },
    mounted() {
        this.getNotifications();
        $("#notCount").remove();
    }
}
</script>

<style>

</style>