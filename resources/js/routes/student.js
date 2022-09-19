import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

// Components
import Dashboard from "../components/Student/dashboard/Dashboard.vue";
import UpcomingExam from "../components/Student/exam/UpcomingExam.vue";
import AttendExam from "../components/Student/exam/AttendExam.vue";
import ExamReport from "../components/Student/exam/ExamReport.vue";
import ResultDetails from "../components/Student/exam/ResultDetails.vue";
import MyProfile from "../components/Student/dashboard/MyProfile.vue";
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
        },
        {
            path: prefix + "my-exams",
            name: "student.exam-report",
            component: ExamReport,
            meta: {
                title : "Exam Report"
            }
        },
        {
            path: prefix + "exam/:examId/result-view",
            name: "student.result-view",
            component: ResultDetails,
            meta: {
                title : "Student Complete result log"
            }
        },
        {
            path: prefix + "my-profile",
            name: "student.my-profile",
            component: MyProfile,
            meta : {
                title: "My profile"
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