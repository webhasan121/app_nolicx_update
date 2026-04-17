import { Head, router, useForm } from "@inertiajs/react";
import GuestLayout from "@/Layouts/GuestLayout";
import PrimaryButton from "@/Components/PrimaryButton";

export default function VerifyEmail({ status = null }) {
    const { post, processing } = useForm({});

    const sendVerification = (e) => {
        e.preventDefault();
        post(route("verification.send"));
    };

    const logout = () => {
        router.post(route("logout.perform"));
    };

    return (
        <GuestLayout>
            <Head title="Email Verification" />

            <div
                className="p-4 bg-white rounded-lg shadow-md"
                style={{ maxWidth: 500 }}
            >
                <div className="mb-4 text-sm text-gray-600">
                    Thanks for signing up! Before getting started, could you
                    verify your email address by clicking on the link we just
                    emailed to you? If you didn&apos;t receive the email, we
                    will gladly send you another.
                </div>

                {status === "verification-link-sent" ? (
                    <div className="mb-4 font-medium text-sm text-green-600">
                        A new verification link has been sent to the email
                        address you provided during registration.
                    </div>
                ) : null}

                <form
                    onSubmit={sendVerification}
                    className="mt-4 flex items-center justify-between"
                >
                    <PrimaryButton disabled={processing}>
                        Resend Verification Email
                    </PrimaryButton>

                    <button
                        onClick={logout}
                        type="button"
                        className="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Log Out
                    </button>
                </form>
            </div>
        </GuestLayout>
    );
}
