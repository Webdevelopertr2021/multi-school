<template>
  <div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <skeleton v-if="isLoading" width="200px" height="25px" />
                <h4 v-else>Wallet {{ wallet }}$</h4>
            </div>
            <div class="card-body">
                <p><strong>All payment history</strong></p>
               <div class="row" v-if="isLoading">
                    <div class="col-12 mb-3" v-for="n in 10" :key="n">
                        <skeleton width="100%" height="40px" />
                    </div>
               </div> 
               <div class="row" v-else>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Payment Date</th>
                                    <th>Amount</th>
                                    <th>Bank name</th>
                                    <th>Reciept Number</th>
                                    <th>Note</th>
                                    <th>Attachment</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(payment,i) in payments" :key="i">
                                    <td>{{ moment(payment.created_at).format("MMMM, YYYY") }}</td>
                                    <td>{{ moment(payment.created_at).format("DD MMMM YYYY") }}</td>
                                    <td>{{ payment.amount }}</td>
                                    <td>{{ payment.bank_name }}</td>
                                    <td>{{ payment.reciept_number }}</td>
                                    <td>{{ payment.note }}</td>
                                    <td>
                                        <a v-if="payment.attachments != null" :href="`/storage/bank/photo/${payment.attachments}`"><i class="fas fa-download"></i> See attachment</a>
                                        <span v-else>N/A</span>
                                    </td>
                                    <td>
                                        <span v-if="payment.status == 'pending'" class="badge badge-warning badge-pill">Waiting to recive</span>
                                        <span v-else class="badge badge-success badge-pill">Money recieved</span>
                                    </td>
                                    <td>
                                        <button @click="acceptMoney(payment.id,i)" v-if="payment.status=='pending'" class="btn btn-sm btn-primary">Mark as recived</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        <pagination :data="paginateData" @pagination-change-page="getHistory"></pagination>
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
            payments: [],
            paginateData: {},
            wallet: 0,
            moment: moment,
        }
    },
    methods: {
        getHistory(page = 1) {
            axios.get("/teacher/api/get-payment-history?page="+page).then(resp=>{
                return resp.data;
            }).then(data=>{
                this.payments = data.history.data;
                this.paginateData = data.history;
                this.wallet = data.wallet;
                this.isLoading = false;
            }).catch(err=>{
                console.error(err.response.data);
            })
        },
        acceptMoney(id,index) {
            swal.fire({
                title: 'Are you sure?',
                text: "Do you want to continue with this action ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post("/teacher/api/accept-payment",{
                        id: id
                    }).then(resp=>{
                        return resp.data;
                    }).then(data=>{
                        if(data.status == "ok") {
                            swal.fire("Success",data.msg,"success");
                            this.payments[index].status = "accepted";
                            this.wallet -= data.amount;
                        }
                    }).catch(err=>{
                        console.error(err.response.data);
                    })
                }
            })
        }
    },
    mounted() {
        this.getHistory();
    }
}
</script>

<style>

</style>