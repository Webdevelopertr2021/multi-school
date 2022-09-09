<template>
  <div class="row justify-content-center">
    <div class="col-md-8 mb-4">
      <ul>
        <li><strong>Credit without salary : </strong> {{ credit_salary }}</li>
        <li><strong>Credit Time : </strong> {{ credit_time }}</li>
      </ul>
    </div>

    <div class="col-md-8 mb-4 d-flex justify-content-between">
      <h3>My Leave Requests</h3>
      <router-link :to="{name: 'teacher.leave-make'}" class="btn btn-primary"><i class="fas fa-plus"></i> Make new leave request</router-link>
    </div>
    <div class="col-md-8">
      <div class="row" v-if="isLoading">
        <div class="col-12 mb-4" v-for="n in 5" :key="n">
          <skeleton width="100%" height="250px" />
        </div>
      </div>

      <div class="row" v-else>

        <div class="col-md-12 mb-4" v-for="(req,i) in paginateData.data" :key="i">
          <div class="card">
            <div class="card-header d-flex justify-content-between">
              <h4>{{ req.subject }}</h4>
              <template v-if="req.status != 'approved'">
                <span v-if="req.status == 'pending'" class="bage badge-pill text-white badge-warning">Pending</span>
                <span v-else-if="req.status == 'rejected'" class="bage badge-pill text-white badge-danger">Rejected</span>
                <span v-else-if="req.status == 'approved'" class="bage badge-pill text-white badge-success">Approved</span>
              </template>
              <template v-else>
                <span v-if="req.approved_by_manager == 1" class="bage badge-pill text-white badge-success">Approved By Manager</span>
                <span v-else class="bage badge-pill text-white badge-warning"><i class="fas fa-clock"></i> Waiting for manager approval</span>
              </template>
            </div>
            <div class="card-body">
              <div class="d-flex">
                <div class="w-50">
                  <p><strong>Vacation Type :</strong> &nbsp; {{ req.vacation_type }}</p>
                  <p><strong>Leave From :</strong> &nbsp; {{ req.from_date }}</p>
                  <p><strong>To :</strong> &nbsp; {{ req.to_date }}</p>
                  <p><strong>Total {{ req.total_days }} Days</strong></p>
                  <p><strong>Application Date :</strong>&nbsp; {{ moment(req.created_at).format('D MMMM YYYY, h:mm A') }}</p>
                </div>
                <div class="ml-3 w-50">
                  <table class="table table-hover">
                    <thead>
                      <tr class="text-center">
                        <th colspan="2">Supervisor Approvals</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="text-center" v-for="(approve,a) in req.approval" :key="a">
                        <td><strong>{{ approve.superv.name }}</strong></td>
                        <td>
                          <span v-if="approve.status=='approved'" class="text-success"><i class="fas fa-check-circle"></i> Approved</span>
                          <span v-if="approve.status=='pending'" class="text-warning"><i class="fas fa-clock"></i> Pending</span>
                          <span v-if="approve.status=='rejected'" class="text-danger"><i class="fas fa-times-circle"></i> Rejected
                            <a href="#" class="btn btn-icon btn-sm btn-primary ml-2" style="border-radius: 50% !important;"
                            data-container="body" data-toggle="popover" data-placement="top" 
                            :data-content="approve.rejection_reason" data-original-title="" title="Rejection reason"
                            ><i class="fas fa-question-circle"></i></a>
                          </span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <p class="mt-4">
                <a class="btn btn-outline-secondary collapsed" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                  Show Apllication Details <i class="fas fa-angle-down"></i>
                </a>
              </p>
              <div class="collapse" id="collapseExample" style="">
                <p>
                  {{ req.desc }}
                </p>
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
      paginateData: {},
      moment: moment,
      credit_salary: 0,
      credit_time: 0,
    }
  },
  methods : {
    getLeaveData(page = 1) {
      this.isLoading = true;
      axios.get("/teacher/api/get-leave-data?page="+page).then(resp=>{
        return resp.data;
      }).then(data=>{
        this.paginateData = data.data;
        this.credit_salary = data.credit_without_salary;
        this.credit_time = data.credit_time;
        this.isLoading = false;
        setTimeout(()=>{
          $('[data-toggle="popover"]').popover({
            container: "body"
          });
        },1000);
      }).catch(err=>{
        console.error(err.response.data);
      })
    }
  },
  mounted() {
    this.getLeaveData();
  }
}
</script>

<style>

</style>