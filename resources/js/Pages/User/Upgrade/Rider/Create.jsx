import { useForm, usePage } from "@inertiajs/react";
import { useEffect, useState } from "react";
import axios from "axios";
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
import useTranslation from "../../../../hooks/useTranslation";

function SearchableSelect({
    id,
    value,
    options = [],
    onChange,
    placeholder,
    disabled = false,
}) {
    const selected = options.find(
        (item) =>
            String(item.name ?? "").trim().toLowerCase() ===
            String(value ?? "").trim().toLowerCase(),
    );
    const [query, setQuery] = useState(selected?.name ?? "");
    const [open, setOpen] = useState(false);

    useEffect(() => {
        setQuery(selected?.name ?? "");
    }, [selected?.name]);

    const filteredOptions = query.trim()
        ? options.filter((item) =>
              String(item.name ?? "")
                  .toLowerCase()
                  .includes(query.trim().toLowerCase()),
          )
        : options;

    const updateQuery = (nextQuery) => {
        setQuery(nextQuery);
        setOpen(true);

        const exactMatch = options.find(
            (item) =>
                String(item.name ?? "").trim().toLowerCase() ===
                nextQuery.trim().toLowerCase(),
        );

        onChange(exactMatch?.name ?? "");
    };

    const selectOption = (item) => {
        setQuery(item.name ?? "");
        onChange(item.name ?? "");
        setOpen(false);
    };

    return (
        <div className="relative">
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
                className="w-full rounded-md border-gray-300 pr-10 disabled:bg-gray-100 disabled:text-gray-500"
            />
            <button
                type="button"
                disabled={disabled}
                onMouseDown={(e) => {
                    e.preventDefault();
                    setOpen((current) => !current);
                }}
                className="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-gray-500 disabled:text-gray-300"
            >
                <i className="fas fa-chevron-down text-xs"></i>
            </button>

            {open && !disabled ? (
                <div className="absolute left-0 top-full z-50 mt-1 max-h-56 w-full overflow-y-auto rounded-md border border-gray-200 bg-white shadow-lg">
                    {filteredOptions.length ? (
                        filteredOptions.map((item) => (
                            <button
                                key={item.id ?? item.name}
                                type="button"
                                onMouseDown={(e) => {
                                    e.preventDefault();
                                    selectOption(item);
                                }}
                                className={`block w-full px-3 py-2 text-left text-sm hover:bg-gray-100 ${
                                    selected?.name === item.name ? "bg-gray-100" : ""
                                }`}
                            >
                                {item.name}
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

export default function UpgradeRiderCreate() {
    const { t } = useTranslation();
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
        const selectedState = states.find(
            (item) =>
                String(item.name ?? "").trim().toLowerCase() ===
                String(data.state_name ?? "").trim().toLowerCase(),
        );

        if (!selectedState) {
            setCities([]);
            setAreas([]);
            return;
        }

        axios
            .get(route("upgrade.rider.cities", { state: selectedState.id }))
            .then((res) => {
                setCities(res.data || []);
            })
            .catch(() => setCities([]));
    }, [data.state_name, states]);

    useEffect(() => {
        const selectedCity = cities.find(
            (item) =>
                String(item.name ?? "").trim().toLowerCase() ===
                String(data.city_name ?? "").trim().toLowerCase(),
        );

        if (!selectedCity) {
            setAreas([]);
            return;
        }

        axios
            .get(route("upgrade.rider.areas", { city: selectedCity.id }))
            .then((res) => {
                setAreas(res.data || []);
            })
            .catch(() => setAreas([]));
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
                            title={t("Rider Request Form")}
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
                        <div className="grid w-full grid-cols-1 gap-4 overflow-hidden lg:grid-cols-2">
                            <SectionSection className="min-w-0">
                                <SectionInner>
                                    <div className="min-w-0 p-2">
                                        <InputFile label={t("Your Phone No")} name="phone" error="phone" errors={errors}>
                                            <TextInput
                                                name="phone"
                                                value={data.phone}
                                                onChange={(e) => setData("phone", e.target.value)}
                                                placeholder={t("Your phone No")}
                                                className="w-full"
                                            />
                                        </InputFile>
                                        <InputFile label={t("Your Email")} name="email" error="email" errors={errors}>
                                            <TextInput
                                                type="email"
                                                name="email"
                                                value={data.email}
                                                onChange={(e) => setData("email", e.target.value)}
                                                placeholder={t("Your email")}
                                                className="w-full"
                                            />
                                        </InputFile>

                                        <Hr />
                                        <InputFile label={t("Your Family Phone No")} name="otherPhone" error="otherPhone" errors={errors}>
                                            <TextInput
                                                name="otherPhone"
                                                value={data.otherPhone}
                                                onChange={(e) => setData("otherPhone", e.target.value)}
                                                placeholder={t("Your Family phone No")}
                                                className="w-full"
                                            />
                                        </InputFile>

                                        <InputFile label={t("Your NID No")} name="nid" error="nid" errors={errors}>
                                            <TextInput
                                                name="nid"
                                                value={data.nid}
                                                onChange={(e) => setData("nid", e.target.value)}
                                                placeholder={t("Your NID No")}
                                                className="w-full"
                                            />
                                        </InputFile>

                                        <InputFile label={t("You NID Front Image (max 1Mb)")} name="nid_photo_front" error="nid_photo_front" errors={errors}>
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

                                        <InputFile label={t("You NID Back Image (max 1Mb)")} name="nid_photo_back" error="nid_photo_back" errors={errors}>
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

                            <SectionSection className="min-w-0">
                                <SectionInner>
                                    <div className="min-w-0 p-2">
                                        <div className="p-2 rounded bg-gray-50">
                                            <div>
                                                <InputFile label={t("Country")} name="country" error="country" errors={errors}>
                                                    <SearchableSelect
                                                        id="country"
                                                        value={data.country}
                                                        options={[{ id: "Bangladesh", name: t("Bangladesh") }]}
                                                        onChange={(value) => setData("country", value)}
                                                        placeholder={t("Country")}
                                                    />
                                                </InputFile>
                                                <Hr />
                                                <InputFile label={t("State")} name="state_name" error="state_name" errors={errors}>
                                                    <SearchableSelect
                                                        id="states"
                                                        value={data.state_name}
                                                        options={states}
                                                        onChange={(value) => {
                                                            setData("state_name", value);
                                                            setData("city_name", "");
                                                            setData("area_name", "");
                                                        }}
                                                        placeholder={t("-- Select State --")}
                                                    />
                                                </InputFile>
                                                <Hr />
                                                <InputFile label={t("City")} name="city_name" error="city_name" errors={errors}>
                                                    <SearchableSelect
                                                        id="city"
                                                        value={data.city_name}
                                                        options={cities}
                                                        onChange={(value) => {
                                                            setData("city_name", value);
                                                            setData("area_name", "");
                                                        }}
                                                        placeholder={t("-- Select City --")}
                                                        disabled={!cities.length}
                                                    />
                                                </InputFile>
                                                <Hr />
                                                <InputFile label={t("Area")} name="area_name" error="area_name" errors={errors}>
                                                    <SearchableSelect
                                                        id="area"
                                                        value={data.area_name}
                                                        options={areas}
                                                        onChange={(value) => setData("area_name", value)}
                                                        placeholder={t("-- Select Area --")}
                                                        disabled={!areas.length}
                                                    />
                                                </InputFile>
                                                <Hr />
                                            </div>
                                            <InputFile label={t("Chose Your Area")} name="area_condition" error="area_condition" errors={errors}>
                                                <div className="w-full max-w-xs space-y-2">
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

                                        <InputFile label={t("Vehicle Type")} name="vehicle_type" error="vehicle_type" errors={errors}>
                                            <TextInput
                                                value={data.vehicle_type}
                                                onChange={(e) => setData("vehicle_type", e.target.value)}
                                                placeholder={t("e.g. Bike, Car")}
                                                className="w-full"
                                            />
                                        </InputFile>
                                        <InputFile label={t("Vehicle Number")} name="vehicle_number" error="vehicle_number" errors={errors}>
                                            <TextInput
                                                value={data.vehicle_number}
                                                onChange={(e) => setData("vehicle_number", e.target.value)}
                                                placeholder={t("e.g. Dhaka Metro 1234")}
                                                className="w-full"
                                            />
                                        </InputFile>
                                        <InputFile label={t("Vehicle Model")} name="vehicle_model" error="vehicle_model" errors={errors}>
                                            <TextInput
                                                value={data.vehicle_model}
                                                onChange={(e) => setData("vehicle_model", e.target.value)}
                                                placeholder={t("e.g. Yamaha YZF-R3")}
                                                className="w-full"
                                            />
                                        </InputFile>
                                        <InputFile label={t("Vehicle Color")} name="vehicle_color" error="vehicle_color" errors={errors}>
                                            <TextInput
                                                value={data.vehicle_color}
                                                onChange={(e) => setData("vehicle_color", e.target.value)}
                                                placeholder={t("e.g. Red")}
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
                                    <InputFile label={t("You Fixed Address")} name="fixed_address" error="fixed_address" errors={errors} className="block">
                                        <p className="text-xs">
                                            Your permanent address based on NID. This address will be used for verification purposes. We use this address to verify your location and provide better service.
                                        </p>
                                        <textarea
                                            value={data.fixed_address}
                                            onChange={(e) => setData("fixed_address", e.target.value)}
                                            className="w-full rounded-md"
                                            placeholder={t("Your Permanent Address based on NID")}
                                        ></textarea>
                                    </InputFile>
                                    <InputFile label={t("You Current Address")} name="current_address" error="current_address" errors={errors}>
                                        <p className="text-xs">{t("Your current address where you are living now. You will receive the parcel from this address.")}</p>
                                        <p className="text-xs">{t("Please provide any additional information about your current address that may help us verify your location.")}</p>
                                        <textarea
                                            value={data.current_address}
                                            onChange={(e) => setData("current_address", e.target.value)}
                                            className="w-full rounded-md"
                                            placeholder={t("Your Current Address")}
                                        ></textarea>
                                    </InputFile>
                                </div>
                                <Hr />
                                <PrimaryButton disabled={processing}>
                                    <i className="pr-2 fas fa-file-alt"></i>{" "}{t("Confirm")}</PrimaryButton>
                            </SectionInner>
                        </SectionSection>
                    </form>
                </div>
            </Container>
        </UserDash>
    );
}
