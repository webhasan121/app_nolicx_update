import { useForm } from "@inertiajs/react";
import { useState } from "react";
import NavLink from "@/Components/NavLink";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";
import PrimaryButton from "@/Components/PrimaryButton";
import GuestLayout from "../../Layouts/GuestLayout";

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: "",
        password: "",
        remember: false,
    });

    const [showPassword, setShowPassword] = useState(false);

    const submit = (e) => {
        e.preventDefault();
        post("/login");
    };

    return (
        <GuestLayout>
            <div className="w-full p-3 bg-white" style={{ maxWidth: "400px" }}>
                <form onSubmit={submit}>
                    {/* Email */}
                    <div>
                        <InputLabel htmlFor="email">Email</InputLabel>
                        <TextInput
                            type="email"
                            className="block w-full mt-1"
                            value={data.email}
                            onChange={(e) => setData("email", e.target.value)}
                            required
                        />
                        <InputError messages={errors.email} className="mt-2" />
                    </div>

                    {/* Password */}
                    <div className="mt-4">
                        <InputLabel htmlFor="password">Password</InputLabel>
                        <div style={{ position: "relative", width: "100%" }}>
                            <TextInput
                                type={showPassword ? "text" : "password"}
                                className="block w-full mt-1"
                                value={data.password}
                                onChange={(e) =>
                                    setData("password", e.target.value)
                                }
                                required
                            />

                            <div
                                onClick={() => setShowPassword(!showPassword)}
                                style={{
                                    position: "absolute",
                                    top: "13px",
                                    right: "10px",
                                    cursor: "pointer",
                                }}
                            >
                                {showPassword ? "hide" : "show"}
                            </div>
                        </div>
                        <InputError
                            messages={errors.password}
                            className="mt-2"
                        />
                    </div>

                    {/* Remember Me */}
                    <div className="block mt-4">
                        <label className="inline-flex items-center">
                            <input
                                type="checkbox"
                                className="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500"
                                checked={data.remember}
                                onChange={(e) =>
                                    setData("remember", e.target.checked)
                                }
                            />
                            <span className="text-sm text-gray-600 ms-2">
                                Remember me
                            </span>
                        </label>
                    </div>
                    <div className="text-center">
                        <NavLink
                            className="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            href={route("password.request")}
                        >
                           Forgot your password?
                        </NavLink>
                    </div>
                    <hr className="my-2" />

                    <div className="flex items-center justify-between mt-4">
                        <NavLink href="/register">Register</NavLink>
                        <PrimaryButton disabled={processing}>
                            Log in
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </GuestLayout>
    );
}
