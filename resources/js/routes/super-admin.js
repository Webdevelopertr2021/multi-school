import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

// Components
import AddSchool from "../components/SuperAdmin/schools/AddSchool.vue";
import SchoolList from "../components/SuperAdmin/schools/SchoolList.vue";
import SchoolDetails from "../components/SuperAdmin/schools/SchoolDetails.vue";
import AddSuperVisor from "../components/SuperAdmin/schools/AddSuperVisor.vue";
import AddTeacher from "../components/SuperAdmin/schools/AddTeacher.vue";
import SupervisorList from "../components/SuperAdmin/supervisor/List.vue";
import EditSupervisor from "../components/SuperAdmin/supervisor/Edit.vue";
import NotificationList from "../components/NotifcationList.vue";
import CreateClass from "../components/SuperAdmin/class/CreateClass.vue";
import CreateSection from "../components/SuperAdmin/class/CreateSection.vue";
import EditSection from "../components/SuperAdmin/class/EditSection.vue";
import CreateStudent from "../components/SuperAdmin/student/Create.vue";
import EditStudent from "../components/SuperAdmin/student/Edit.vue";
import Listtudent from "../components/SuperAdmin/student/List.vue";
// End
const prefix = "/admin/"
const routes = new VueRouter({
    mode: "history",
    linkExactActiveClass: "active",
    linkActiveClass: "active",
    routes: [
        // {
        //     path: "/dashboard",
        //     component: ()=>import(""),
        //     name: 'admin.dashboard'
        // },
        
        // School
        {
            path: prefix + "add-school",
            component: AddSchool,
            name: "admin.add-school",
            meta: {
                title: "Add new school"
            }
        },
        {
            path: prefix+ "school-list",
            component: SchoolList,
            name: "admin.school-list",
            meta: {
                title : "All schools"
            }
        },
        {
            path: prefix+ "school-details/:schoolId",
            component: SchoolDetails,
            name: "admin.school-details",
            meta: {
                title: "School details"
            }
        },
        {
            path: prefix + "add-supervisor/school/:schoolId",
            name: "admin.add-supervisor",
            component: AddSuperVisor,
            meta: {
                title : "Add supervisors"
            }
        },
        {
            path: prefix + "add-teacher/school/:schoolId",
            name: "admin.add-teacher",
            component: AddTeacher,
            meta: {
                title : "Add teacher"
            }
        },
        {
            path: prefix + "supervisor-list",
            name: "admin.supervisor-list",
            component: SupervisorList,
            meta: {
                title : "Supervisor list"
            }
        },
        {
            path : prefix + "edit-supervisor-profile/user/:userId",
            name: "admin.edit-superv",
            component: EditSupervisor,
            meta : {
                title : "Edit supervisor"
            }
        },
        {
            path: prefix + "notifications",
            name: "notification",
            component: NotificationList,
            meta: {
                title : "My notifications"
            }
        },
        {
            path: prefix + "create-class",
            name: "admin.create-class",
            component: CreateClass,
            meta: {
                title: "Create class"
            }
        },
        {
            path: prefix + "create-section",
            name: "admin.create-section",
            component: CreateSection,
            meta: {
                title: "Create section"
            }
        },
        {
            path: prefix + "edit-section/:sectionId",
            name: "admin.edit-section",
            component: EditSection,
            meta : {
                title: "Edit section"
            }
        },
        {
            path: prefix + "add-students",
            name: "admin.add-student",
            component:CreateStudent,
            meta: {
                title: "Add Student"
            }
        },
        {
            path: prefix + "student-list",
            name: "admin.student-list",
            component: Listtudent,
            meta: {
                title : "Student List"
            }
        },
        {
            path: prefix + "edit-student/:studentId",
            name: "admin.edit-student",
            component: EditStudent,
            meta : {
                title : "Edit student list"
            }
        }
    ],
    scrollBehavior(to, from, savedPos) {
        if (savedPos) {
            return savedPos;
        } else {
            return { x: 0, y: 0 };
        }
    }
});

routes.beforeEach((to, from, next) => {
    document.title = to.meta.title || "Dashboard";
    Vue.prototype.$Progress.start();
    next();
});

routes.afterEach(() => {
    Vue.prototype.$Progress.finish();
});

export default routes;