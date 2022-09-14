<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Leave Requests</h4>
            </div>
            <div class="card-body">
                <div class="row" v-if="isLoading">
                  <div class="col-12 mb-2" v-for="n in 10" :key="n">
                    <skeleton width="100%" height="40px" />
                  </div>
                </div>
                <div class="row" v-else>
                  <div class="col-12">
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                          <tr class="text-center">
                            <th>Photo</th>
                            <th>Requested by</th>
                            <th>Subject</th>
                            <th>Vacation Type</th>
                            <th>Date range</th>
                            <th>Total</th>
                            <th>Application date</th>
                            <th>Status</th>
                            <th>Manager Approval</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <template v-if="paginateData.data.length <=0">
                            <tr class="text-center">
                              <td colspan="9" class="text-danger">No data found</td>
                            </tr>
                          </template>
                          <template v-else>
                            <tr class="text-center" v-for="(req,i) in paginateData.data" :key="i">
                              <td>
                                <img v-if="req.teacher.photo == null" class="user-thumb-40" src="/image/portrait-placeholder.png" alt="">
                                <img v-else class="user-thumb-40" :src="req.teacher.photo_url" alt="">
                              </td>
                              <td><b>{{ req.teacher.name }}</b></td>
                              <td>{{ req.leave.subject }}</td>
                              <td>{{ req.leave.vacation_type }}</td>
                              <td>{{ req.leave.from_date }} to {{ req.leave.to_date }}</td>
                              <td>{{ req.leave.total_days }}</td>
                              <td>{{ moment(req.leave.created_at).format("DD MMMM YYYY") }}</td>
                              <td>
                                <span v-if="req.status=='pending'" class="badge badge-pill badge-warning">Pending</span>
                                <span v-else-if="req.status=='rejected'" class="badge badge-pill badge-danger">Rejected</span>
                                <span v-else-if="req.status == 'approved'" class="badge badge-pill badge-success">Approved</span>
                              </td>
                              <td>
                                <span v-if="req.leave.approved_by_manager == 1" class="badge badge-pill badge-success">Approved</span>
                                <span v-else-if="req.leave.approved_by_manager == 2" class="badge badge-pill badge-danger">Rejected</span>
                                <span v-else class="badge badge-pill badge-warning">Pending</span>
                              </td>
                              <td><button v-if="req.status == 'approved'" class="btn btn-primary btn-sm" @click="actionModal(req,i)">Take action <i class="fas fa-arrow-right"></i></button></td>
                            </tr>
                          </template>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 d-flex justify-content-center">
                    <pagination :data="paginateData" @pagination-change-page="getList"></pagination>
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
    }
  },
  methods : {
    getList(page = 1) {
      axios.get("/manager/api/get-leave-request?page="+page).then(resp=>{
        return resp.data;
      }).then(data=>{
        if(data.status == "ok") {
          console.log(data.leaves);
          this.paginateData = data.leaves;
          this.isLoading = false;
        }
        else {
          this.$router.push({
            name: 'superv.dashboard'
          });
        }
      }).catch(err=>{
        console.error(err.response.data);
      });
    },
    actionModal(data,index) {
      swal.fire({
        title: data.leave.subject,
        html: `
          <hr>
          <div class="col-12 mb-4">
            <p class="text-left">${data.leave.desc}</p>
          </div>
          <hr>
          <div class="col-12 mb-4">
            <label class="text-left"><b>Choose action</b></label>
            <select id="actionStatus" class="form-control">
              <option value="approved" ${data.approved_by_manager==1?'selected':''}>Approve</option>
              <option value="rejected" ${data.approved_by_manager==0?'selected':''}>Reject</option>
            </select>
          </div>
          <div class="col-12 mb-4">
            <label class="text-left"><b>Write feedback message</b></label>
            <textarea class="form-control" id="actionText" placeholder="Feedback message..."></textarea>
          </div>
          `,
        confirmButtonText: 'Submit',
        focusConfirm: false,
        preConfirm: () => {
          const status = swal.getPopup().querySelector('#actionStatus').value
          const msg = swal.getPopup().querySelector('#actionText').value

          return { status: status, msg: msg }
        }
      }).then((result) => {
        if(result.isConfirmed) {
          axios.post("/manager/api/update-application-status",{
            status: result.value.status,
            msg: result.value.msg,
            id: data.id,
            reqId: data.leave.id,
          }).then(resp=>{
            return resp.data;
          }).then(data=>{
            if(data.status == "ok") {
              swal.fire("Data updated",data.msg,"success").then(()=>{
                window.location.reload();
              });
            }
          }).catch(err=>{
            console.error(err.response.data);
          });
        }
      })
    }
  },
  mounted () {
    this.getList();
  }
}
</script>

<style>

</style>