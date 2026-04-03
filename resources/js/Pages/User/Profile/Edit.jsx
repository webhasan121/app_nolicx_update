import { useForm, usePage } from "@inertiajs/react";
import { useEffect, useState } from "react";
import Container from "../../../components/dashboard/Container";
import SectionSection from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import UserDash from "../../../components/user/dash/UserDash";
import InputLabel from "../../../components/InputLabel";
import TextInput from "../../../components/TextInput";
import InputError from "../../../components/InputError";
import PrimaryButton from "../../../components/PrimaryButton";
import Hr from "../../../components/Hr";

export default function ProfileEdit() {
    const {
        userProfile,
        countries = [],
        states: initialStates = [],
        cities: initialCities = [],
        genders = {},
        passwordRules = [],
    } = usePage().props;

    const [states, setStates] = useState(initialStates);
    const [cities, setCities] = useState(initialCities);

    const profileForm = useForm({
        name: userProfile?.name || "",
        email: userProfile?.email || "",
        phone: userProfile?.phone || "",
        bio: userProfile?.bio || "",
        dob: userProfile?.dob || "",
        gender: userProfile?.gender || "",
        country: userProfile?.country || "",
        state: userProfile?.state || "",
        city: userProfile?.city || "",
        line1: userProfile?.line1 || "",
        line2: userProfile?.line2 || "",
        zip: userProfile?.zip || "",
    });

    const passwordForm = useForm({
        current_password: "",
        password: "",
        password_confirmation: "",
    });

    useEffect(() => {
        if (!profileForm.data.country) {
            setStates([]);
            setCities([]);
            return;
        }

        axios
            .get(route("edit.profile.states", { country: profileForm.data.country }))
            .then((res) => {
                setStates(res.data || []);
            });
    }, [profileForm.data.country]);

    useEffect(() => {
        if (!profileForm.data.state) {
            setCities([]);
            return;
        }

        axios
            .get(route("edit.profile.cities", { state: profileForm.data.state }))
            .then((res) => {
                setCities(res.data || []);
            });
    }, [profileForm.data.state]);

    const submitProfile = (e) => {
        e.preventDefault();
        profileForm.post(route("edit.profile.update"));
    };

    const submitPassword = (e) => {
        e.preventDefault();
        passwordForm.post(route("edit.profile.password"), {
            onSuccess: () =>
                passwordForm.reset(
                    "current_password",
                    "password",
                    "password_confirmation",
                ),
        });
    };

    const sendVerification = () => {
        profileForm.post(route("edit.profile.verification"));
    };

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionHeader
                        title="Profile Update"
                        content={
                            <p className="text-sm md:text-base">
                                Update your account&apos;s profile information and
                                email address.
                            </p>
                        }
                    />

                    <SectionInner>
                        <form onSubmit={submitProfile} className="mt-6 space-y-6">
                            <div className="flex flex-col justify-between gap-8 lg:flex-row">
                                <div className="relative w-full p-4 space-y-4 bg-green-50 rounded-md shadow-md lg:w-1/3 lg:space-y-6">
                                    <div className="pb-2 mb-4 border-b border-gray-200">
                                        <h3 className="text-lg font-semibold text-gray-700">
                                            Personal Information
                                        </h3>
                                        <p className="mt-1 text-sm text-gray-500">
                                            Update your basic profile details.
                                        </p>
                                    </div>

                                    <div className="relative">
                                        <InputLabel htmlFor="name">Name</InputLabel>
                                        <TextInput
                                            value={profileForm.data.name}
                                            onChange={(e) => profileForm.setData("name", e.target.value)}
                                            type="text"
                                            className="block w-full mt-1"
                                            required
                                        />
                                        <InputError className="mt-2" messages={profileForm.errors.name} />
                                    </div>

                                    <div className="relative">
                                        <InputLabel htmlFor="email">Email</InputLabel>
                                        <TextInput
                                            value={profileForm.data.email}
                                            onChange={(e) => profileForm.setData("email", e.target.value)}
                                            type="email"
                                            className="block w-full mt-1"
                                            required
                                        />
                                        <InputError className="mt-2" messages={profileForm.errors.email} />

                                        {userProfile?.must_verify_email && !userProfile?.email_verified && (
                                            <div>
                                                <p className="mt-2 text-sm text-gray-800">
                                                    <span>Your email address is unverified.</span>
                                                    <button
                                                        type="button"
                                                        onClick={sendVerification}
                                                        className="text-sm text-gray-600 underline rounded-md hover:text-gray-900"
                                                    >
                                                        Click here to re-send the verification email.
                                                    </button>
                                                </p>
                                            </div>
                                        )}
                                    </div>

                                    <div className="relative">
                                        <InputLabel htmlFor="phone">Phone</InputLabel>
                                        <TextInput
                                            value={profileForm.data.phone}
                                            onChange={(e) => profileForm.setData("phone", e.target.value)}
                                            type="text"
                                            className="block w-full mt-1"
                                        />
                                        <InputError className="mt-2" messages={profileForm.errors.phone} />
                                    </div>

                                    <div className="relative">
                                        <InputLabel htmlFor="dob">Date of Birth</InputLabel>
                                        <TextInput
                                            value={profileForm.data.dob || ""}
                                            onChange={(e) => profileForm.setData("dob", e.target.value)}
                                            type="date"
                                            className="block w-full mt-1"
                                        />
                                        <InputError className="mt-2" messages={profileForm.errors.dob} />
                                    </div>

                                    <div className="relative">
                                        <InputLabel htmlFor="gender">Gender</InputLabel>
                                        <select
                                            value={profileForm.data.gender}
                                            onChange={(e) => profileForm.setData("gender", e.target.value)}
                                            className="block w-full mt-1 border-0 rounded ring-1"
                                        >
                                            <option value="">Select Gender</option>
                                            {Object.entries(genders).map(([key, value]) => (
                                                <option key={key} value={key}>
                                                    {value}
                                                </option>
                                            ))}
                                        </select>
                                        <InputError className="mt-2" messages={profileForm.errors.gender} />
                                    </div>
                                </div>

                                <div className="relative w-full p-4 space-y-4 bg-green-50 rounded-md shadow-md lg:w-2/3">
                                    <div className="relative">
                                        <InputLabel htmlFor="bio">Bio</InputLabel>
                                        <textarea
                                            value={profileForm.data.bio}
                                            onChange={(e) => profileForm.setData("bio", e.target.value)}
                                            rows="6"
                                            className="block w-full mt-1 border-0 rounded ring-1 ring-gray-300 resize-none"
                                            placeholder="Write something about yourself..."
                                        ></textarea>
                                        <InputError className="mt-2" messages={profileForm.errors.bio} />
                                    </div>
                                    <Hr />

                                    <div className="relative">
                                        <InputLabel htmlFor="line1">Address</InputLabel>
                                        <TextInput
                                            value={profileForm.data.line1}
                                            onChange={(e) => profileForm.setData("line1", e.target.value)}
                                            type="text"
                                            className="block w-full mt-1"
                                        />
                                        <TextInput
                                            value={profileForm.data.line2}
                                            onChange={(e) => profileForm.setData("line2", e.target.value)}
                                            type="text"
                                            className="block w-full mt-4"
                                        />
                                        <InputError className="mt-2" messages={profileForm.errors.line1} />
                                        <InputError className="mt-2" messages={profileForm.errors.line2} />
                                    </div>
                                    <Hr />

                                    <div className="grid gap-4 md:grid-cols-2">
                                        <div className="relative">
                                            <InputLabel htmlFor="country">Country</InputLabel>
                                            <select
                                                value={profileForm.data.country || ""}
                                                onChange={(e) => {
                                                    profileForm.setData("country", e.target.value);
                                                    profileForm.setData("state", "");
                                                    profileForm.setData("city", "");
                                                }}
                                                className="block w-full mt-1 border-0 rounded ring-1"
                                            >
                                                <option value="">Select Country</option>
                                                {countries.map((country) => (
                                                    <option key={country.id} value={country.id}>
                                                        {country.name}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError className="mt-2" messages={profileForm.errors.country} />
                                        </div>

                                        <div className="relative">
                                            <InputLabel htmlFor="state">State</InputLabel>
                                            <select
                                                value={profileForm.data.state || ""}
                                                onChange={(e) => {
                                                    profileForm.setData("state", e.target.value);
                                                    profileForm.setData("city", "");
                                                }}
                                                className="block w-full mt-1 border-0 rounded ring-1"
                                            >
                                                <option value="">Select State</option>
                                                {states.map((stateItem) => (
                                                    <option key={stateItem.id} value={stateItem.id}>
                                                        {stateItem.name}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError className="mt-2" messages={profileForm.errors.state} />
                                        </div>

                                        <div className="relative">
                                            <InputLabel htmlFor="city">City</InputLabel>
                                            <select
                                                value={profileForm.data.city || ""}
                                                onChange={(e) => profileForm.setData("city", e.target.value)}
                                                className="block w-full mt-1 border-0 rounded ring-1"
                                            >
                                                <option value="">Select City</option>
                                                {cities.map((city) => (
                                                    <option key={city.id} value={city.id}>
                                                        {city.name}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError className="mt-2" messages={profileForm.errors.city} />
                                        </div>

                                        <div className="relative">
                                            <InputLabel htmlFor="zip">Zip Code</InputLabel>
                                            <TextInput
                                                value={profileForm.data.zip}
                                                onChange={(e) => profileForm.setData("zip", e.target.value)}
                                                type="text"
                                                className="block w-full mt-1"
                                            />
                                            <InputError className="mt-2" messages={profileForm.errors.zip} />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="flex items-center gap-4">
                                <PrimaryButton disabled={profileForm.processing}>Save</PrimaryButton>
                            </div>
                        </form>
                    </SectionInner>
                </SectionSection>

                <SectionSection>
                    <SectionHeader
                        title="Update Password"
                        content={
                            <p className="text-sm md:text-base">
                                Ensure your account is using a long, random password to stay secure.
                            </p>
                        }
                    />

                    <SectionInner>
                        <div className="grid gap-6 mt-6 lg:grid-cols-2">
                            <div className="relative">
                                <form onSubmit={submitPassword} className="p-4 space-y-4 bg-green-50 rounded-md shadow-md">
                                    <div className="relative">
                                        <InputLabel htmlFor="current_password">Current Password</InputLabel>
                                        <TextInput
                                            value={passwordForm.data.current_password}
                                            onChange={(e) => passwordForm.setData("current_password", e.target.value)}
                                            type="password"
                                            className="block w-full mt-1"
                                        />
                                        <InputError messages={passwordForm.errors.current_password} className="mt-2" />
                                    </div>

                                    <div className="relative">
                                        <InputLabel htmlFor="password">New Password</InputLabel>
                                        <TextInput
                                            value={passwordForm.data.password}
                                            onChange={(e) => passwordForm.setData("password", e.target.value)}
                                            type="password"
                                            className="block w-full mt-1"
                                        />
                                        <InputError messages={passwordForm.errors.password} className="mt-2" />
                                    </div>

                                    <div className="relative">
                                        <InputLabel htmlFor="password_confirmation">Confirm Password</InputLabel>
                                        <TextInput
                                            value={passwordForm.data.password_confirmation}
                                            onChange={(e) => passwordForm.setData("password_confirmation", e.target.value)}
                                            type="password"
                                            className="block w-full mt-1"
                                        />
                                        <InputError messages={passwordForm.errors.password_confirmation} className="mt-2" />
                                    </div>

                                    <div className="flex items-center gap-4">
                                        <PrimaryButton disabled={passwordForm.processing}>Save</PrimaryButton>
                                    </div>
                                </form>
                            </div>

                            <div className="relative">
                                <div className="p-4 space-y-6 bg-red-50 rounded-md shadow-md">
                                    <div className="pb-2 mb-2 border-b border-blue-200">
                                        <h3 className="text-lg font-semibold text-blue-700">Password Rules</h3>
                                        <p className="text-sm text-blue-600">
                                            Follow these rules when setting a new password.
                                        </p>
                                    </div>

                                    <div className="mt-4">
                                        <h3 className="mb-2 font-medium text-gray-700">Password Requirements:</h3>
                                        <ul className="space-y-3 text-sm text-gray-600 list-disc list-inside lg:text-base">
                                            {passwordRules.map((rule) => (
                                                <li key={rule}>{rule}</li>
                                            ))}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </SectionInner>
                </SectionSection>

                <SectionSection>
                    <SectionHeader
                        title="Delete Account"
                        content={
                            <p className="text-sm md:text-base">
                                Once your account is deleted, all of its resources and data will be permanently deleted.
                            </p>
                        }
                    />
                    <SectionInner></SectionInner>
                </SectionSection>
            </Container>
        </UserDash>
    );
}
