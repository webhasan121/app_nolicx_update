import { useForm, usePage } from "@inertiajs/react";
import { useEffect, useState } from "react";
import Container from "../../../../components/dashboard/Container";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Hr from "../../../../components/Hr";
import InputFile from "../../../../components/InputFile";
import InputLabel from "../../../../components/InputLabel";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import UserDash from "../../../../components/user/dash/UserDash";

export default function UpgradeRiderCreate() {
    const { defaults = {}, states = [] } = usePage().props;

    const { data, setData, post, processing, errors } = useForm({
        phone: defaults.phone || "",
        email: defaults.email || "",
        otherPhone: "",
        nid: "",
        nid_photo_front: null,
        nid_photo_back: null,
        fixed_address: "",
        current_address: "",
        area_condition: "dhaka",
        area_name: "",
        state_name: defaults.state_name || "",
        city_name: defaults.city_name || "",
        country: defaults.country || "Bangladesh",
        vehicle_type: "",
        vehicle_number: "",
        vehicle_model: "",
        vehicle_color: "",
    });

    const [cities, setCities] = useState([]);
    const [areas, setAreas] = useState([]);

    useEffect(() => {
        const selectedState = states.find((item) => item.name === data.state_name);

        if (!selectedState) {
            setCities([]);
            setAreas([]);
            return;
        }

        axios
            .get(route("upgrade.rider.cities", { state: selectedState.id }))
            .then((res) => {
                setCities(res.data || []);
            });
    }, [data.state_name, states]);

    useEffect(() => {
        const selectedCity = cities.find((item) => item.name === data.city_name);

        if (!selectedCity) {
            setAreas([]);
            return;
        }

        axios
            .get(route("upgrade.rider.areas", { city: selectedCity.id }))
            .then((res) => {
                setAreas(res.data || []);
            });
    }, [data.city_name, cities]);

    const submit = (e) => {
        e.preventDefault();
        post(route("upgrade.rider.store"), {
            forceFormData: true,
        });
    };

    return (
        <UserDash>
            <Container>
                <div>
                    <SectionSection>
                        <SectionHeader
                            title="Rider Request Form"
                            content={
                                <NavLinkBtn href={route("upgrade.rider.index")}>
                                    previous request
                                </NavLinkBtn>
                            }
                        />
                    </SectionSection>

                    <form
                        onSubmit={submit}
                        encType="multipart/form-data"
                        className="w-full"
                    >
                        <div className="gap-2 md:flex">
                            <SectionSection>
                                <SectionInner>
                                    <div className="flex-1 p-2">
                                        <InputFile label="Your Phone No" name="phone" error="phone" errors={errors}>
                                            <TextInput
                                                name="phone"
                                                value={data.phone}
                                                onChange={(e) => setData("phone", e.target.value)}
                                                placeholder="Your phone No"
                                                className="w-full"
                                            />
                                        </InputFile>
                                        <InputFile label="Your Email" name="email" error="email" errors={errors}>
                                            <TextInput
                                                type="email"
                                                name="email"
                                                value={data.email}
                                                onChange={(e) => setData("email", e.target.value)}
                                                placeholder="Your email"
                                                className="w-full"
                                            />
                                        </InputFile>

                                        <Hr />
                                        <InputFile label="Your Family Phone No" name="otherPhone" error="otherPhone" errors={errors}>
                                            <TextInput
                                                name="otherPhone"
                                                value={data.otherPhone}
                                                onChange={(e) => setData("otherPhone", e.target.value)}
                                                placeholder="Your Family phone No"
                                                className="w-full"
                                            />
                                        </InputFile>

                                        <InputFile label="Your NID No" name="nid" error="nid" errors={errors}>
                                            <TextInput
                                                name="nid"
                                                value={data.nid}
                                                onChange={(e) => setData("nid", e.target.value)}
                                                placeholder="Your NID No"
                                                className="w-full"
                                            />
                                        </InputFile>

                                        <InputFile label="You NID Front Image (max 1Mb)" name="nid_photo_front" error="nid_photo_front" errors={errors}>
                                            <div>
                                                {data.nid_photo_front && (
                                                    <img
                                                        src={URL.createObjectURL(data.nid_photo_front)}
                                                        alt="NID Front"
                                                        style={{
                                                            width: "200px",
                                                            height: "100px",
                                                        }}
                                                    />
                                                )}
                                                <TextInput
                                                    type="file"
                                                    onChange={(e) => setData("nid_photo_front", e.target.files[0])}
                                                    id="nid_front"
                                                    max="1024"
                                                />
                                            </div>
                                        </InputFile>

                                        <InputFile label="You NID Back Image (max 1Mb)" name="nid_photo_back" error="nid_photo_back" errors={errors}>
                                            <div>
                                                {data.nid_photo_back && (
                                                    <img
                                                        src={URL.createObjectURL(data.nid_photo_back)}
                                                        alt="NID Back"
                                                        style={{
                                                            width: "200px",
                                                            height: "100px",
                                                        }}
                                                    />
                                                )}
                                                <TextInput
                                                    type="file"
                                                    onChange={(e) => setData("nid_photo_back", e.target.files[0])}
                                                    id="nid_back"
                                                    max="1024"
                                                />
                                            </div>
                                        </InputFile>
                                    </div>
                                </SectionInner>
                            </SectionSection>

                            <SectionSection>
                                <SectionInner>
                                    <div className="flex-1 p-2">
                                        <div className="p-2 rounded bg-gray-50">
                                            <div>
                                                <InputFile label="Country" name="country" error="country" errors={errors}>
                                                    <select
                                                        value={data.country}
                                                        onChange={(e) => setData("country", e.target.value)}
                                                        id="country"
                                                        className="w-full rounded-md "
                                                    >
                                                        <option value="Bangladesh">Bangladesh</option>
                                                    </select>
                                                </InputFile>
                                                <Hr />
                                                <InputFile label="State" name="state_name" error="state_name" errors={errors}>
                                                    <select
                                                        value={data.state_name}
                                                        onChange={(e) => {
                                                            setData("state_name", e.target.value);
                                                            setData("city_name", "");
                                                            setData("area_name", "");
                                                        }}
                                                        id="states"
                                                        className="w-full rounded-md "
                                                    >
                                                        <option value=""> -- Select State --</option>
                                                        {states.map((state) => (
                                                            <option key={state.id} value={state.name}>
                                                                {state.name}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </InputFile>
                                                <Hr />
                                        <InputFile label="City" name="city_name" error="city_name" errors={errors}>
                                            <select
                                                value={data.city_name}
                                                onChange={(e) => {
                                                    setData("city_name", e.target.value);
                                                    setData("area_name", "");
                                                }}
                                                id="city"
                                                className="w-full rounded-md "
                                            >
                                                <option value=""> -- Select City --</option>
                                                {cities.map((item) => (
                                                    <option key={item.id} value={item.name}>
                                                        {item.name}
                                                    </option>
                                                ))}
                                                    </select>
                                                </InputFile>
                                                <Hr />
                                                <InputFile label="Area" name="area_name" error="area_name" errors={errors}>
                                            <select
                                                value={data.area_name}
                                                onChange={(e) => setData("area_name", e.target.value)}
                                                id="area"
                                                className="w-full rounded-md "
                                            >
                                                <option value=""> -- Select Area --</option>
                                                {areas.map((item) => (
                                                    <option key={item.id} value={item.name}>
                                                        {item.name}
                                                    </option>
                                                ))}
                                                    </select>
                                                </InputFile>
                                                <Hr />
                                            </div>
                                            <InputFile label="Chose Your Area" name="area_condition" error="area_condition" errors={errors}>
                                                <div className="w-48 space-y-2">
                                                    <div className="flex items-center justify-start px-3 py-2 border rounded-lg shadow-sm">
                                                        <TextInput
                                                            style={{ width: "20px", height: "20px" }}
                                                            type="radio"
                                                            name="area_condition"
                                                            className="m-0 mr-3"
                                                            value="dhaka"
                                                            checked={data.area_condition === "dhaka"}
                                                            onChange={(e) => setData("area_condition", e.target.value)}
                                                            id="area_condition_1"
                                                        />
                                                        <InputLabel htmlFor="area_condition_1" className="m-0">
                                                            Inside of Dhaka
                                                        </InputLabel>
                                                    </div>
                                                    <div className="flex items-center justify-start px-3 py-2 border rounded-lg shadow-sm">
                                                        <TextInput
                                                            style={{ width: "20px", height: "20px" }}
                                                            type="radio"
                                                            name="area_condition"
                                                            className="m-0 mr-3"
                                                            value="other"
                                                            checked={data.area_condition === "other"}
                                                            onChange={(e) => setData("area_condition", e.target.value)}
                                                            id="area_condition_2"
                                                        />
                                                        <InputLabel htmlFor="area_condition_2" className="m-0">
                                                            Outside Of Dhaka
                                                        </InputLabel>
                                                    </div>
                                                </div>
                                            </InputFile>
                                        </div>

                                        <InputFile label="Vehicle Type" name="vehicle_type" error="vehicle_type" errors={errors}>
                                            <TextInput
                                                value={data.vehicle_type}
                                                onChange={(e) => setData("vehicle_type", e.target.value)}
                                                placeholder="e.g. Bike, Car"
                                                className="w-full"
                                            />
                                        </InputFile>
                                        <InputFile label="Vehicle Number" name="vehicle_number" error="vehicle_number" errors={errors}>
                                            <TextInput
                                                value={data.vehicle_number}
                                                onChange={(e) => setData("vehicle_number", e.target.value)}
                                                placeholder="e.g. Dhaka Metro 1234"
                                                className="w-full"
                                            />
                                        </InputFile>
                                        <InputFile label="Vehicle Model" name="vehicle_model" error="vehicle_model" errors={errors}>
                                            <TextInput
                                                value={data.vehicle_model}
                                                onChange={(e) => setData("vehicle_model", e.target.value)}
                                                placeholder="e.g. Yamaha YZF-R3"
                                                className="w-full"
                                            />
                                        </InputFile>
                                        <InputFile label="Vehicle Color" name="vehicle_color" error="vehicle_color" errors={errors}>
                                            <TextInput
                                                value={data.vehicle_color}
                                                onChange={(e) => setData("vehicle_color", e.target.value)}
                                                placeholder="e.g. Red"
                                                className="w-full"
                                            />
                                        </InputFile>
                                    </div>
                                </SectionInner>
                            </SectionSection>
                        </div>

                        <SectionSection>
                            <SectionInner>
                                <div>
                                    <InputFile label="You Fixed Address" name="fixed_address" error="fixed_address" errors={errors} className="block">
                                        <p className="text-xs">
                                            Your permanent address based on NID. This address will be used for verification purposes. We use this address to verify your location and provide better service.
                                        </p>
                                        <textarea
                                            value={data.fixed_address}
                                            onChange={(e) => setData("fixed_address", e.target.value)}
                                            className="w-full rounded-md"
                                            placeholder="Your Permanent Address based on NID "
                                        ></textarea>
                                    </InputFile>
                                    <InputFile label="You Current Address" name="current_address" error="current_address" errors={errors}>
                                        <p className="text-xs">
                                            Your current address where you are living now. You will receive the parcel from this address.
                                        </p>
                                        <p className="text-xs">
                                            Please provide any additional information about your current address that may help us verify your location.
                                        </p>
                                        <textarea
                                            value={data.current_address}
                                            onChange={(e) => setData("current_address", e.target.value)}
                                            className="w-full rounded-md"
                                            placeholder="Your Current Address"
                                        ></textarea>
                                    </InputFile>
                                </div>
                                <Hr />
                                <PrimaryButton disabled={processing}>
                                    <i className="pr-2 fas fa-file-alt"></i>{" "}
                                    Confirm
                                </PrimaryButton>
                            </SectionInner>
                        </SectionSection>
                    </form>
                </div>
            </Container>
        </UserDash>
    );
}
