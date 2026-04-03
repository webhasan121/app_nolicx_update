import { useForm, usePage } from "@inertiajs/react";
import { useState, useEffect } from "react";
import axios from "axios";

import GuestLayout from "../../Layouts/GuestLayout";
import InputLabel from "../../components/InputLabel";
import TextInput from "../../components/TextInput";
import InputError from "../../components/InputError";
import NavLink from "../../components/NavLink";
import PrimaryButton from "../../components/PrimaryButton";

export default function Register() {
    const { countries } = usePage().props;

    const { data, setData, post, processing, errors } = useForm({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        phone: "",
        reference: "",
        country_id: "",
        state_id: "",
        city_id: "",
    });

    const [states, setStates] = useState([]);
    const [cities, setCities] = useState([]);

    const [showPassword, setShowPassword] = useState(false);
    const [showConfirm, setShowConfirm] = useState(false);

    // 🔥 Load States when Country changes
    useEffect(() => {
        if (data.country_id) {
            axios.get(`/states/${data.country_id}`).then((res) => {
                setStates(res.data);
                setCities([]);
                setData("state_id", "");
                setData("city_id", "");
            });
        }
    }, [data.country_id]);

    // 🔥 Load Cities when State changes
    useEffect(() => {
        if (data.state_id) {
            axios.get(`/cities/${data.state_id}`).then((res) => {
                setCities(res.data);
                setData("city_id", "");
            });
        }
    }, [data.state_id]);

    const submit = (e) => {
        e.preventDefault();
        post("/register");
    };

    return (
        <GuestLayout>
            <section
                className="p-8 bg-white rounded-md lg:w-2/5"
                style={{ maxWidth: "800px" }}
            >
                <form onSubmit={submit}>
                    {/* Name */}
                    <div className="relative">
                        <InputLabel htmlFor="name">Name</InputLabel>
                        <TextInput
                            id="name"
                            className="w-full mt-1"
                            value={data.name}
                            onChange={(e) => setData("name", e.target.value)}
                        />
                        <InputError messages={errors.name} />
                    </div>

                    {/* Email */}
                    <div className="relative">
                        <InputLabel htmlFor="email" className="mt-4">
                            Email
                        </InputLabel>
                        <TextInput
                            id="email"
                            className="w-full mt-1"
                            value={data.email}
                            onChange={(e) => setData("email", e.target.value)}
                        />
                        <InputError messages={errors.email} />
                    </div>

                    <div className="grid gap-4 mt-4 lg:grid-cols-2">
                        {/* Password */}
                        <div className="relative">
                            <InputLabel htmlFor="password">Password</InputLabel>
                            <TextInput
                                id="password"
                                type={showPassword ? "text" : "password"}
                                className="w-full mt-1"
                                value={data.password}
                                onChange={(e) =>
                                    setData("password", e.target.value)
                                }
                            />

                            <button
                                type="button"
                                className="absolute flex items-center text-gray-500 top-9 right-2"
                                onClick={() => setShowPassword(!showPassword)}
                            >
                                <i
                                    className={`fas ${
                                        showPassword ? "fa-eye-slash" : "fa-eye"
                                    }`}
                                />
                            </button>

                            <InputError messages={errors.password} />
                        </div>

                        {/* Confirm Password */}
                        <div className="relative">
                            <InputLabel htmlFor="password_confirmation">
                                Confirm Password
                            </InputLabel>

                            <TextInput
                                id="password_confirmation"
                                type={showConfirm ? "text" : "password"}
                                className="w-full mt-1"
                                value={data.password_confirmation}
                                onChange={(e) =>
                                    setData(
                                        "password_confirmation",
                                        e.target.value,
                                    )
                                }
                            />

                            <button
                                type="button"
                                className="absolute flex items-center text-gray-500 top-9 right-2"
                                onClick={() => setShowConfirm(!showConfirm)}
                            >
                                <i
                                    className={`fas ${
                                        showConfirm ? "fa-eye-slash" : "fa-eye"
                                    }`}
                                />
                            </button>
                        </div>

                        {/* Phone */}
                        <div className="relative">
                            <InputLabel htmlFor="phone">Phone</InputLabel>
                            <TextInput
                                id="phone"
                                className="w-full mt-1"
                                value={data.phone}
                                onChange={(e) =>
                                    setData("phone", e.target.value)
                                }
                            />
                            <InputError messages={errors.phone} />
                        </div>

                        {/* Reference */}
                        <div className="relative">
                            <InputLabel htmlFor="reference">
                                Reference (optional)
                            </InputLabel>
                            <TextInput
                                id="reference"
                                className="w-full mt-1"
                                value={data.reference}
                                onChange={(e) =>
                                    setData("reference", e.target.value)
                                }
                            />
                        </div>

                        {/* Country */}
                        <div className="relative">
                            <InputLabel htmlFor="country">Country</InputLabel>

                            <select
                                id="country"
                                className="w-full mt-1 border-gray-300 rounded-md"
                                value={data.country_id}
                                onChange={(e) =>
                                    setData("country_id", e.target.value)
                                }
                            >
                                <option value="">-- Select Country --</option>

                                {countries.map((country) => (
                                    <option key={country.id} value={country.id}>
                                        {country.name}
                                    </option>
                                ))}
                            </select>

                            <InputError messages={errors.country_id} />
                        </div>

                        {/* State */}
                        <div className="relative">
                            <InputLabel htmlFor="state">
                                State / District
                            </InputLabel>

                            <select
                                id="state"
                                className="w-full mt-1 border-gray-300 rounded-md"
                                value={data.state_id}
                                onChange={(e) =>
                                    setData("state_id", e.target.value)
                                }
                                disabled={!states.length}
                            >
                                <option value="">-- Select State --</option>

                                {states.map((state) => (
                                    <option key={state.id} value={state.id}>
                                        {state.name}
                                    </option>
                                ))}
                            </select>

                            <InputError messages={errors.state_id} />
                        </div>

                        {/* City */}
                        <div className="relative">
                            <InputLabel htmlFor="city">
                                City (optional)
                            </InputLabel>

                            <select
                                id="city"
                                className="w-full mt-1 border-gray-300 rounded-md"
                                value={data.city_id}
                                onChange={(e) =>
                                    setData("city_id", e.target.value)
                                }
                                disabled={!cities.length}
                            >
                                <option value="">-- Select City --</option>

                                {cities.map((city) => (
                                    <option key={city.id} value={city.id}>
                                        {city.name}
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div className="flex items-center justify-between mt-6">
                        <div>
                            <p>Already have an account!</p>

                            <NavLink href="/login">Login</NavLink>
                        </div>

                        <PrimaryButton disabled={processing}>
                            Register
                        </PrimaryButton>
                    </div>
                </form>
            </section>
        </GuestLayout>
    );
}
