<template>
  <div class="row">
    <div class="col-md-12 text-center mb-2">
        <h1>All Staffs</h1>
    </div>

    <template v-if="isLoading">
        <div class="col-md-4 mb-4 tree-skeleton" v-for="n in 6" :key="n">
            <ul>
                <li>
                    <skeleton width="200px" height="40px" />
                    <ul>
                        <li>
                            <skeleton width="170px" height="35px" />
                            <ul>
                                <li><skeleton width="180px" height="30px" /></li>
                                <li><skeleton width="180px" height="30px" /></li>
                                <li><skeleton width="180px" height="30px" /></li>
                                <li><skeleton width="180px" height="30px" /></li>
                            </ul>
                        </li>
                        <li>
                            <skeleton width="170px" height="35px" />
                            <ul>
                                <li><skeleton width="180px" height="30px" /></li>
                                <li><skeleton width="180px" height="30px" /></li>
                                <li><skeleton width="180px" height="30px" /></li>
                                <li><skeleton width="180px" height="30px" /></li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </template>

    <template v-else>
        <div class="col-md-4" v-for="(manager,i) in treeData" :key="i">
            <div id="test" class="tree">
                <ul>
                    <li class="parent_li">
                        <span title="Manager" class="manager-tag">
                            <img v-if="manager.photo == null" class="thumb-30" src="/image/portrait-placeholder.png" alt="">
                            <img v-else class="thumb-30" :src="manager.photo_url">
                            {{ manager.name }}
                        </span>
                        <ul>
                            <li class="parent_li" v-for="(superv,j) in manager.supervisors" :key="j">
                                <span title="Supervisor" class="superv-tag">
                                    <img v-if="superv.photo == null" class="thumb-30" src="/image/portrait-placeholder.png" alt="">
                                    <img v-else class="thumb-30" :src="superv.photo_url">
                                    {{ superv.name }}
                                </span>
                                <ul>
                                    <li class="parent_li" v-for="(teacher,t) in superv.teachers" :key="t">
                                        <span title="Teacher" class="teacher-tag">
                                            <img v-if="teacher.photo == null" class="thumb-30" src="/image/portrait-placeholder.png" alt="">
                                            <img v-else class="thumb-30" :src="teacher.photo_url">
                                            {{ teacher.name }}
                                        </span>
                                    </li>
                                </ul>
                            </li>                            
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </template>

  </div>
</template>

<script>
export default {
    data() {
        return {
            isLoading: true,
            treeData: [],
        }
    },
    methods : {
        getTreeData() {
            axios.get("/admin/api/get-tree-data").then(resp=>{
                return resp.data;
            }).then(data=>{
                this.treeData = data;
                this.isLoading = false;
            }).catch(err=>{
                console.error(err.response.data);
            });
        }
    },
    mounted() {
        this.getTreeData();
    }
}
</script>

<style>

</style>