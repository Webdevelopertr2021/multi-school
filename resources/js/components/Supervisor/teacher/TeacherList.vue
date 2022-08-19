<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>My Teachers</h4>
            </div>
            <div class="card-body">
                <div class="row" v-if="isLoading">
                    <div class="col-12 mb-2" v-for="n in 10" :key="n">
                        <skeleton width="100%" height="35px" />
                    </div>
                </div>
                <div class="row" v-else>
                    <div class="col-12">
                        <div class="">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr class="text-center">
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Supervisors</th>
                                        <th>Ratings</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="teachers.length <= 0">
                                        <tr class="text-center">
                                            <td colspan="6" class="text-danger">No teacher found</td>
                                        </tr>
                                    </template>
                                    <template v-else>
                                        <tr class="text-center" v-for="(teacher,i) in teachers" :key="i">
                                            <td>
                                                <img v-if="teacher.photo_url==null" src="/image/portrait-placeholder.png" alt="" class="user-thumb-40">
                                                <img v-else :src="teacher.photo_url" alt="" class="user-thumb-40">
                                            </td>
                                            <td>{{ teacher.name }}</td>
                                            <td>{{ teacher.phone }}</td>
                                            <td>{{ teacher.email }}</td>
                                            <td>
                                                <span v-for="(superv,s) in teacher.supervisor" :key="s" class="badge badge-pill badge-success ml-1 mb-3">{{ superv.user.name }}</span>
                                            </td>
                                            <td>
                                                {{ teacher.rating_this_month }} points <span v-html="teacher.rating_stat"></span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <router-link class="dropdown-item" :to="{name: 'superv.teacher-ratings', params: {userId: teacher.id}}"><i class="fas fa-star"></i> See ratings</router-link>
                                                        <router-link :to="{name: 'superv.add-rating', params: {userId: teacher.id}}" class="dropdown-item"><i class="fas fa-star"></i> Add / Edit Review</router-link>
                                                        <router-link :to="{name: 'superv.studentlist-by-teacher', params: {teacherId: teacher.id}}" class="dropdown-item"><i class="fas fa-users"></i> See Students</router-link>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script>

export default {
    data() {
        return {
            isLoading: true,
            teachers: [],
        }
    },
    methods: {
        getTeacherList() {
            axios.get("/supervisor/api/get-teacher-list").then(resp=>{
                return resp.data;
            }).then(data=>{
                this.teachers = data;
                this.isLoading = false;
            }).catch(err=>{
                console.error(err.response.data);
            });
        },
        calculateRating(ratings) {
            let totalRates = 0;
            let totalPoints = 0;
            ratings.forEach((item,i)=>{
                totalRates+=5;
                totalPoints += item.rate1;
                totalPoints += item.rate2;
                totalPoints += item.rate3;
                totalPoints += item.rate4;
                totalPoints += item.rate5;
            });

            return totalPoints/totalRates;
        }
    },
    mounted() {
        this.getTeacherList();
    }
}
</script>

<style>

</style>