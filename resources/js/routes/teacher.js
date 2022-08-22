import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

// Components
import LeaveContainer from "../components/Teacher/LeaveRequest/Container.vue";
import LeaveList from "../components/Teacher/LeaveRequest/List.vue";
import MakeRequest from "../components/Teacher/LeaveRequest/MakeRequest.vue";
import NotificationList from "../components/NotifcationList.vue";
import MyStudent from "../components/Teacher/Students/List.vue";
import StudentRatings from "../components/Teacher/Students/StudentRatings.vue";
import StudentSubmitReview from "../components/Teacher/Students/SubmitReview.vue";
import Dashboard from "../components/Teacher/dashboard/Dashboard.vue";
import MyProfile from "../components/Teacher/dashboard/MyProfile.vue";
import MyPayment from "../components/Teacher/payments/payments.vue";
// End

const prefix = "/teacher/"
const routes = new VueRouter({
    mode: "history",
    linkExactActiveClass: "active",
    linkActiveClass: "active",
    routes: [
        {
            path: prefix + "dashboard",
            name: "teacher.home",
            component: Dashboard,
            meta: {
                title: "Teacher dashboard"
            }
        },
        {
            path: prefix + "leave-request",
            name: "teacher.leave",
            component: LeaveContainer,
            redirect: {
                name: "teacher.leave-list"
            },
            children: [
                {
                    path: "/",
                    name: "teacher.leave-list",
                    component:LeaveList,
                    meta: {
                        title: "My Leave request"
                    }
                },
                {
                    path: "make-new-request",
                    name: "teacher.leave-make",
                    component: MakeRequest,
                    meta: {
                        title: "Create leave request"
                    }
                },
                {
                    path: prefix+ "my-students",
                    name: "teacher.my-students",
                    component: MyStudent,
                    meta : {
                        title: "My students"
                    }
                },
                {
                    path : prefix + "student-ratings/:studentId",
                    name: "teacher.student-ratings",
                    component: StudentRatings,
                    meta: {
                        title: "Student Ratings"
                    }
                },
                {
                    path: prefix + "submit-review/student/:studentId",
                    name: "teacher.submit-review",
                    component: StudentSubmitReview,
                    meta : {
                        title: "Submit Review"
                    }
                },
                {
                    path: prefix + "my-profile",
                    name: "teacher.my-profile",
                    component: MyProfile,
                    meta : {
                        title: "My profile"
                    }
                }
            ]
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
            path: prefix + "my-payments",
            name: "teacher.my-payments",
            component: MyPayment,
            meta : {
                title : "My payments"
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