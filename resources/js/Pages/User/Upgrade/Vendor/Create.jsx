import { useForm, usePage } from "@inertiajs/react";
import { useEffect, useState } from "react";
import Container from "../../../../components/dashboard/Container";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Hr from "../../../../components/Hr";
import InputFile from "../../../../components/InputFile";
import InputField from "../../../../components/InputField";
import InputLabel from "../../../../components/InputLabel";
import NavLinkBtn from "../../../../components/NavLinkBtn";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import UserDash from "../../../../components/user/dash/UserDash";
import useTranslation from "../../../../hooks/useTranslation";

export default function UpgradeVendorCreate() {
    const { t } = useTranslation();
    const { upgrade = "vendor", defaults = {}, states = [] } = usePage().props;
    const [cities, setCities] = useState([]);

    const { data, setData, post, processing, errors } = useForm({
        upgrade,
        shop_name_en: "",
        shop_name_bn: "",
        phone: defaults.phone || "",
        email: defaults.email || "",
        country: defaults.country || "Bangladesh",
        district: "",
        upozila: "",
        village: "",
        zip: "",
        road_no: "",
        house_no: "",
        address: "",
        logo: null,
        banner: null,
        description: "",
    });

    useEffect(() => {
        const selectedState = states.find((item) => item.name === data.district);

        if (!selectedState) {
            setCities([]);
            return;
        }

        axios
            .get(route("upgrade.vendor.cities", { state: selectedState.id }))
            .then((res) => {
                setCities(res.data || []);
            });
    }, [data.district, states]);

    const submit = (e) => {
        e.preventDefault();
        post(route("upgrade.vendor.store"), {
            forceFormData: true,
        });
    };

    return (
        <UserDash>
            <Container>
                <div>
                    <SectionSection>
                        <SectionHeader
                            title={
                                <div className="flex justify-between">
                                    <div>{t("Open")}{" "}
                                        {upgrade.charAt(0).toUpperCase() +
                                            upgrade.slice(1)}{" "}{t("Shop")}</div>
                                    <NavLinkBtn
                                        href={route("upgrade.vendor.index", {
                                            upgrade,
                                        })}
                                        className=""
                                    >
                                        <i className="fas fa-list pr-2"></i> All
                                    </NavLinkBtn>
                                </div>
                            }
                            content={`Request to set-up a ${
                                upgrade.charAt(0).toUpperCase() +
                                upgrade.slice(1)
                            } shop. Shop allows you to sell your products to other Users. It allows you to reach a wider audience and increase your sales potential.`}
                        />
                    </SectionSection>

                    <form onSubmit={submit} method="post">
                        <SectionSection>
                            <SectionInner>
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    label={t("Your Shop Name")}
                                    name="shop_name_en"
                                    error={errors.shop_name_en}
                                    value={data.shop_name_en}
                                    onChange={(e) =>
                                        setData("shop_name_en", e.target.value)
                                    }
                                />

                                <Hr />

                                <InputFile
                                    label={t("Logo (Max 1Mb)")}
                                    error="logo"
                                    errors={errors}
                                >
                                    <p>{t("100x100 logo")}</p>
                                    <div
                                        style={{ width: "100px", height: "100px" }}
                                        className="border rounded"
                                    >
                                        {data.logo && (
                                            <img
                                                style={{
                                                    width: "100px",
                                                    height: "100px",
                                                }}
                                                className="border rounded shadow"
                                                src={URL.createObjectURL(data.logo)}
                                                alt="100x100"
                                            />
                                        )}
                                    </div>
                                    <div className="relative">
                                        <TextInput
                                            onChange={(e) =>
                                                setData("logo", e.target.files[0])
                                            }
                                            type="file"
                                            id="logo"
                                            className="absolute hidden"
                                        />
                                        <label
                                            htmlFor="logo"
                                            className="p-2 border rounded shadow"
                                        >
                                            <i className="fas fa-upload"></i>
                                        </label>
                                    </div>
                                </InputFile>

                                <InputFile
                                    label={t("Banner (Max 1Mb)")}
                                    error="banner"
                                    errors={errors}
                                >
                                    <p>{t("100x300 banner image")}</p>
                                    <div
                                        style={{ width: "300px", height: "100px" }}
                                        className="border rounded"
                                    >
                                        {data.banner && (
                                            <img
                                                style={{
                                                    width: "300px",
                                                    height: "100px",
                                                }}
                                                className="border rounded shadow"
                                                src={URL.createObjectURL(
                                                    data.banner,
                                                )}
                                                alt="100x300"
                                            />
                                        )}
                                    </div>
                                    <div className="relative">
                                        <TextInput
                                            onChange={(e) =>
                                                setData(
                                                    "banner",
                                                    e.target.files[0],
                                                )
                                            }
                                            type="file"
                                            id="banner"
                                            className="absolute hidden"
                                        />
                                        <label
                                            htmlFor="banner"
                                            className="p-2 border rounded shadow"
                                        >
                                            <i className="fas fa-upload"></i>
                                        </label>
                                    </div>
                                </InputFile>

                                <InputFile
                                    label={t("Description")}
                                    error="description"
                                    errors={errors}
                                >
                                    <textarea
                                        value={data.description}
                                        onChange={(e) =>
                                            setData("description", e.target.value)
                                        }
                                        id="description"
                                        className="w-full border rounded"
                                        rows="5"
                                        placeholder={t("Describe about your shop...")}
                                    ></textarea>
                                </InputFile>
                                <Hr />
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    type="number"
                                    label={t("Your Shop Phone")}
                                    name="phone"
                                    error={errors.phone}
                                    value={data.phone}
                                    onChange={(e) =>
                                        setData("phone", e.target.value)
                                    }
                                />
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    type="email"
                                    label={t("Your Shop email")}
                                    name="email"
                                    error={errors.email}
                                    value={data.email}
                                    onChange={(e) =>
                                        setData("email", e.target.value)
                                    }
                                />
                            </SectionInner>
                        </SectionSection>

                        <SectionSection>
                            <SectionInner>
                                <p className="my-1">{t("Shop Location")}</p>

                                <div className="mt-4">
                                    <div style={{ width: "350px" }}>
                                        <InputLabel htmlFor="address">
                                            Give Full Address Of Your Shops
                                        </InputLabel>
                                    </div>

                                    <div className="w-full">
                                        <textarea
                                            name="address"
                                            id="address"
                                            value={data.address}
                                            onChange={(e) =>
                                                setData("address", e.target.value)
                                            }
                                            className="w-full rounded"
                                            rows="1"
                                            placeholder={t("Full Address")}
                                        ></textarea>
                                        {errors.address && (
                                            <div className="mt-2 text-sm text-red-600">
                                                {errors.address}
                                            </div>
                                        )}
                                    </div>
                                </div>
                                <Hr />

                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    label={t("Village")}
                                    name="village"
                                    error={errors.village}
                                    value={data.village}
                                    onChange={(e) =>
                                        setData("village", e.target.value)
                                    }
                                />
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    label={t("Zip Code")}
                                    name="zip"
                                    error={errors.zip}
                                    value={data.zip}
                                    onChange={(e) =>
                                        setData("zip", e.target.value)
                                    }
                                />
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    label={t("Road No")}
                                    name="road_no"
                                    error={errors.road_no}
                                    value={data.road_no}
                                    onChange={(e) =>
                                        setData("road_no", e.target.value)
                                    }
                                />
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    label={t("House No")}
                                    name="house_no"
                                    error={errors.house_no}
                                    value={data.house_no}
                                    onChange={(e) =>
                                        setData("house_no", e.target.value)
                                    }
                                />

                                <div className="items-center mt-4 md:flex">
                                    <div style={{ width: "350px" }}>
                                        <InputLabel htmlFor="country">
                                            Your Country
                                        </InputLabel>
                                        {errors.country && (
                                            <div className="mt-2 text-sm text-red-600">
                                                {errors.country}
                                            </div>
                                        )}
                                    </div>

                                    <div className="w-full">
                                        <select
                                            value={data.country}
                                            onChange={(e) =>
                                                setData("country", e.target.value)
                                            }
                                            id="country"
                                            className="block w-full mt-1 border-0 rounded ring-1"
                                        >
                                            <option value="Bangladesh">{t("Bangladesh")}</option>
                                        </select>
                                    </div>
                                </div>

                                <div className="items-center mt-4 md:flex">
                                    <div style={{ width: "350px" }}>
                                        <InputLabel htmlFor="district">
                                            District
                                        </InputLabel>
                                        {errors.district && (
                                            <div className="mt-2 text-sm text-red-600">
                                                {errors.district}
                                            </div>
                                        )}
                                    </div>

                                    <select
                                        value={data.district}
                                        onChange={(e) => {
                                            setData("district", e.target.value);
                                            setData("upozila", "");
                                        }}
                                        id="district"
                                        className="w-full rounded-md"
                                    >
                                        <option value="">
                                            {" "}{t("-- Select Upozila --")}</option>
                                        {states.map((state) => (
                                            <option
                                                key={state.id}
                                                value={state.name}
                                            >
                                                {state.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div className="mt-4 md:flex">
                                    <div style={{ width: "350px" }}>
                                        <InputLabel htmlFor="upozila">
                                            Upozila
                                        </InputLabel>
                                        {errors.upozila && (
                                            <div className="mt-2 text-sm text-red-600">
                                                {errors.upozila}
                                            </div>
                                        )}
                                    </div>

                                    <div className="w-full">
                                        <select
                                            value={data.upozila}
                                            onChange={(e) =>
                                                setData("upozila", e.target.value)
                                            }
                                            id="upozila"
                                            className="w-full rounded-md"
                                        >
                                            <option value="">
                                                {" "}{t("-- Select Upozila --")}</option>
                                            {cities.map((item) => (
                                                <option
                                                    key={item.id}
                                                    value={item.name}
                                                >
                                                    {item.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                </div>

                                <br />
                                <PrimaryButton disabled={processing}>{t("Submit")}</PrimaryButton>
                            </SectionInner>
                        </SectionSection>
                    </form>
                </div>
            </Container>
        </UserDash>
    );
}

