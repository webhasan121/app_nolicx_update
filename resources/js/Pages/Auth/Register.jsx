import { useForm, usePage } from "@inertiajs/react";
import { useState, useEffect } from "react";
import axios from "axios";

import GuestLayout from "../../Layouts/GuestLayout";
import InputLabel from "../../components/InputLabel";
import TextInput from "../../components/TextInput";
import InputError from "../../components/InputError";
import NavLink from "../../components/NavLink";
import PrimaryButton from "../../components/PrimaryButton";

function SearchableSelect({
    id,
    value,
    options = [],
    onChange,
    placeholder,
    disabled = false,
}) {
    const selected = options.find((option) => String(option.id) === String(value));
    const [query, setQuery] = useState(selected?.name ?? "");
    const [open, setOpen] = useState(false);

    useEffect(() => {
        setQuery(selected?.name ?? "");
    }, [selected?.id, selected?.name]);

    const filteredOptions = query.trim()
        ? options.filter((option) =>
              String(option.name ?? "")
                  .toLowerCase()
                  .includes(query.trim().toLowerCase()),
          )
        : options;

    const updateQuery = (nextQuery) => {
        setQuery(nextQuery);
        setOpen(true);

        const exactMatch = options.find(
            (option) =>
                String(option.name ?? "").toLowerCase() ===
                nextQuery.trim().toLowerCase(),
        );

        onChange(exactMatch?.id ?? "");
    };

    const selectOption = (option) => {
        setQuery(option.name ?? "");
        onChange(option.id);
        setOpen(false);
    };

    return (
        <div className="relative mt-1">
            <input
                id={id}
                type="text"
                value={query}
                onChange={(e) => updateQuery(e.target.value)}
                onFocus={() => !disabled && setOpen(true)}
                onBlur={() => window.setTimeout(() => setOpen(false), 150)}
                placeholder={placeholder}
                disabled={disabled}
                autoComplete="off"
                className="w-full border-gray-300 rounded-md disabled:bg-gray-100 disabled:text-gray-500"
            />
            <button
                type="button"
                disabled={disabled}
                onMouseDown={(e) => {
                    e.preventDefault();
                    setOpen((current) => !current);
                }}
                className="absolute inset-y-0 right-0 flex items-center justify-center w-10 text-gray-500 disabled:text-gray-300"
            >
                <i className="fas fa-chevron-down text-xs"></i>
            </button>

            {open && !disabled ? (
                <div className="absolute z-50 w-full mt-1 overflow-y-auto bg-white border border-gray-200 rounded-md shadow-lg max-h-52">
                    {filteredOptions.length ? (
                        filteredOptions.map((option) => (
                            <button
                                key={option.id}
                                type="button"
                                onMouseDown={(e) => {
                                    e.preventDefault();
                                    selectOption(option);
                                }}
                                className="block w-full px-3 py-2 text-sm text-left hover:bg-gray-100"
                            >
                                {option.name}
                            </button>
                        ))
                    ) : (
                        <div className="px-3 py-2 text-sm text-gray-500">
                            No results found
                        </div>
                    )}
                </div>
            ) : null}
        </div>
    );
}

export default function Register() {
    const { countries = [] } = usePage().props;

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
        } else {
            setStates([]);
            setCities([]);
            setData("state_id", "");
            setData("city_id", "");
        }
    }, [data.country_id]);

    // 🔥 Load Cities when State changes
    useEffect(() => {
        if (data.state_id) {
            axios.get(`/cities/${data.state_id}`).then((res) => {
                setCities(res.data);
                setData("city_id", "");
            });
        } else {
            setCities([]);
            setData("city_id", "");
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

                            <SearchableSelect
                                id="country"
                                value={data.country_id}
                                options={countries}
                                onChange={(value) => setData("country_id", value)}
                                placeholder="-- Select Country --"
                            />

                            <InputError messages={errors.country_id} />
                        </div>

                        {/* State */}
                        <div className="relative">
                            <InputLabel htmlFor="state">
                                State / District
                            </InputLabel>

                            <SearchableSelect
                                id="state"
                                value={data.state_id}
                                options={states}
                                onChange={(value) => setData("state_id", value)}
                                placeholder="-- Select State --"
                                disabled={!states.length}
                            />

                            <InputError messages={errors.state_id} />
                        </div>

                        {/* City */}
                        <div className="relative">
                            <InputLabel htmlFor="city">
                                City (optional)
                            </InputLabel>

                            <SearchableSelect
                                id="city"
                                value={data.city_id}
                                options={cities}
                                onChange={(value) => setData("city_id", value)}
                                placeholder="-- Select City --"
                                disabled={!cities.length}
                            />
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
