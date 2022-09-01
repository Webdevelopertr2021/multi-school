<template lang="">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>All Exam lists</h4>
                </div>
                <div class="card-body">
                    <div class="row" v-if="isLoading">
                        <div class="col-md-12 mb-3" v-for="n in 10" :key="n">
                            <skeleton width="100%" height="40px"></skeleton>
                        </div>
                    </div>
                    <div class="row" v-else>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th>SL</th>
                                            <th>Exam Title</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>School Info</th>
                                            <th>Created By</th>
                                            <th>Exam created at</th>
                                            <th>Status</th>
                                            <th>Total Questions</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(exam,i) in exams" :key="i" class="text-center">
                                            <td>{{ i+1 }}</td>
                                            <td>{{ exam.title }}</td>
                                            <td>{{ moment(exam.start_time).format("DD MMMM YYYY, h:mm A") }}</td>
                                            <td>{{ moment(exam.end_time).format("DD MMMM YYYY, h:mm A") }}</td>
                                            <td><b>{{ exam.school.name }}</b><br>Class : {{ exam.classes.name }} - Section : {{ exam.section.name }}</td>
                                            <td>{{ exam.created_by }}</td>
                                            <td>{{ moment(exam.created_at).format("DD MMM, YYYY - h:mm a") }}</td>
                                            <td>{{ exam.status }}</td>
                                            <td>15</td>
                                            <td>
                                                <router-link :to="{name: 'admin.exam-questions', params: {examId: exam.id}}" class="btn btn-sm btn-secondary" title="Add question"><i class="fas fa-plus"></i></router-link>
                                                <button class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
                                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
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
            exams: [],
            examData: {},
            isLoading: true,
            moment: moment,
        }
    },
    methods: {
        getExams() {
            axios.get("/admin/api/get-exam-list").then(resp=>{
                return resp.data;
            }).then(data=>{
                this.exams = data.data;
                this.examData = data;
                this.isLoading = false;
            }).catch(err=>{
                console.error(err.response.data);
            })
        }
    },
    mounted() {
        this.getExams();
    }
}
</script>
<style lang="">
    
</style>