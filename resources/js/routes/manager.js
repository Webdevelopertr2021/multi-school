import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

// Components
import Dashboard from "../components/Manager/home/Dashboard.vue";
import SuperVisorList from "../components/Manager/supervisor/List.vue";
import StudentList from "../components/Manager/students/List.vue";
import TeacherList from "../components/Manager/teacher/TeacherList.vue";
import StudentListByTeacher from "../components/Manager/teacher/StudentListByTeacher.vue";
import TeacherListSuperv from "../components/Manager/supervisor/Teachers.vue";
import LeaveRequest from "../components/Manager/LeaveRequest/List.vue";
import TeacherRatings from "../components/Manager/teacher/AllRatings.vue";
import StudentRatings from "../components/Manager/students/StudentRatings.vue";
import MyProfile from "../components/Manager/dashboard/MyProfile.vue";
// End

const prefix = "/manager/"
const routes = new VueRouter({
    mode: "history",
    linkExactActiveClass: "active",
    linkActiveClass: "active",
    routes: [
        {
            path: prefix + "dashboard",
            name: "manager.dashboard",
            component: Dashboard,
            meta: {
                title : "Manager - Dashboard"
            }
        },
        {
            path: prefix + "supervisors",
            name: "manager.supervisors",
            component: SuperVisorList,
            meta : {
                title : "Supervisors List"
            }
        },
        {
            path: prefix + "student-list",
            name: "manager.student-list",
            component: StudentList,
            meta: {
                title: "Student List"
            }
        },
        {
            path: prefix + "teacher-list",
            name: "manager.teacher-list",
            component: TeacherList,
            meta: {
                title: "Teacher List"
            }
        },
        {
            path : prefix + "teacher/:teacherId/student-list",
            name: "manager.studentlist-by-teacher",
            component: StudentListByTeacher,
            meta : {
                title: "Students"
            }
        },
        {
            path: prefix + "supervisor/:supervisorId/teachers",
            name: "manager.superv.teachers",
            component: TeacherListSuperv,
            meta : {
                title : "Supervisor List"
            }
        },
        {
            path: prefix + 'leave-requests',
            name: "manager.leave-request",
            component: LeaveRequest,
            meta: {
                title: "Leave request",
            }
        },
        {
            path: prefix + "teacher-ratings/:teacherId",
            name: "manager.teacher-ratings",
            component: TeacherRatings,
            meta: {
                title: "Teacher Rating"
            }
        },
        {
            path: prefix + "student-ratings/:studentId",
            name: "manager.student-rating",
            component: StudentRatings,
            meta: {
                title: "Student ratings"
            }
        },
        {
            path: prefix + "my-profile",
            name: "manager.my-profile",
            component: MyProfile,
            meta : {
                title: "My profile"
            }
        },
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