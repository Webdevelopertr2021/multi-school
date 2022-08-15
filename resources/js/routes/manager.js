import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

// Components
import Dashboard from "../components/Manager/home/Dashboard.vue";
import SuperVisorList from "../components/Manager/supervisor/List.vue";
import StudentList from "../components/Manager/students/List.vue";
import TeacherList from "../components/Manager/teacher/TeacherList.vue";
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