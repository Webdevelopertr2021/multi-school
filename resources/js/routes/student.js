import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

// Components
import Dashboard from "../components/Student/dashboard/Dashboard.vue";
import UpcomingExam from "../components/Student/exam/UpcomingExam.vue";
import AttendExam from "../components/Student/exam/AttendExam.vue";
// End

const prefix = "/student/"
const routes = new VueRouter({
    mode: "history",
    linkExactActiveClass: "active",
    linkActiveClass: "active",
    routes: [
        {
            path: prefix + "dashboard",
            name: "student.dashboard",
            component: Dashboard,
            meta : {
                title: "Student Dashboard"
            }
        },
        {
            path: prefix + "upcoming-exams",
            name: "student.upcoming-exam",
            component: UpcomingExam,
            meta: {
                title : "Upcoming exams"
            }
        },
        {
            path: prefix + "attend-exam/exam/:examId",
            name: "student.attend",
            component: AttendExam,
            meta: {
                title: "Exam",
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