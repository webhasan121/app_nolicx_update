import { Head, useForm } from "@inertiajs/react";
import GuestLayout from "../../Layouts/GuestLayout";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";

export default function ResetPassword({ token = "", email = "" }) {
    const { data, setData, post, processing, errors } = useForm({
        token,
        email: email ?? "",
        password: "",
        password_confirmation: "",
    });

    const submit = (e) => {
        e.preventDefault();
        post(route("password.update"));
    };

    return (
        <GuestLayout>
            <Head title="Reset Password" />

            <div>
                <form onSubmit={submit}>
                    <div>
                        <InputLabel htmlFor="email">Email</InputLabel>
                        <TextInput
                            id="email"
                            type="email"
                            name="email"
                            className="block mt-1 w-full"
                            value={data.email}
                            onChange={(e) => setData("email", e.target.value)}
                            required
                            autoFocus
                            autoComplete="username"
                        />
                        <InputError messages={errors.email} className="mt-2" />
                    </div>

                    <div className="mt-4">
                        <InputLabel htmlFor="password">Password</InputLabel>
                        <TextInput
                            id="password"
                            type="password"
                            name="password"
                            className="block mt-1 w-full"
                            value={data.password}
                            onChange={(e) =>
                                setData("password", e.target.value)
                            }
                            required
                            autoComplete="new-password"
                        />
                        <InputError
                            messages={errors.password}
                            className="mt-2"
                        />
                    </div>

                    <div className="mt-4">
                        <InputLabel htmlFor="password_confirmation">
                            Confirm Password
                        </InputLabel>
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            className="block mt-1 w-full"
                            value={data.password_confirmation}
                            onChange={(e) =>
                                setData("password_confirmation", e.target.value)
                            }
                            required
                            autoComplete="new-password"
                        />
                        <InputError
                            messages={errors.password_confirmation}
                            className="mt-2"
                        />
                    </div>

                    <div className="flex items-center justify-end mt-4">
                        <PrimaryButton disabled={processing}>
                            Reset Password
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </GuestLayout>
    );
}
