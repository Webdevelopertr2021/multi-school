import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

// Components
import SupervDashboard from "../components/Supervisor/dashboard/Dashboard.vue";
import TeacherList from "../components/Supervisor/teacher/TeacherList.vue";
import Rating from "../components/Supervisor/rating/Rating.vue";
import LeaveRequest from "../components/Supervisor/LeaveRequest/List.vue";
import NotificationList from "../components/NotifcationList.vue";
import AllRatings from "../components/Supervisor/teacher/AllRatings.vue";
import MyProfile from "../components/Supervisor/dashboard/MyProfile.vue";
import StudentListByTeacher from "../components/Supervisor/teacher/StudentListByTeacher.vue";
import StudentRatings from "../components/Supervisor/student/AllRatings.vue";
// End
const prefix = "/supervisor/"
const routes = new VueRouter({
    mode: "history",
    linkExactActiveClass: "active",
    linkActiveClass: "active",
    routes: [
        {
            path: prefix + "dashboard",
            name: 'superv.dashboard',
            component : SupervDashboard,
            meta: {
                title: "Supervisor - Dashboard"
            }
        },
        {
            path: prefix + "my-teacher",
            name: "superv.teacher-list",
            component: TeacherList,
            meta: {
                title: "My Teachers"
            }
        },
        {
            path: prefix + "add-rating/teacher/:userId",
            name: "superv.add-rating",
            component: Rating,
            meta : {
                title : "Add review"
            }
        },
        {
            path: prefix + 'leave-requests',
            name: "superv.leave-request",
            component: LeaveRequest,
            meta: {
                title: "Leave request",
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
            path: prefix + "teacher/:userId/ratings",
            name: "superv.teacher-ratings",
            component: AllRatings,
            meta: {
                title : "Teacher ratings"
            }
        },
        {
            path: prefix + "my-profile",
            name: "superv.my-profile",
            component: MyProfile,
            meta : {
                title: "My Profile"
            }
        },
        {
            path : prefix + "teacher/:teacherId/student-list",
            name: "superv.studentlist-by-teacher",
            component: StudentListByTeacher,
            meta : {
                title: "Students"
            }
        },
        {
            path : prefix + "student-ratings/:studentId",
            name: "superv.student-ratings",
            component: StudentRatings,
            meta: {
                title: "Student Ratings"
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