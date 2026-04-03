import { useForm, usePage } from "@inertiajs/react";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import InputField from "../../../../components/InputField";
import InputFile from "../../../../components/InputFile";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import UserDash from "../../../../components/user/dash/UserDash";
import UpgradeStatus from "../../../../components/client/UpgradeStatus";
import Hr from "../../../../components/Hr";

export default function UpgradeVendorEdit() {
    const {
        id,
        upgrade = "vendor",
        nav = "basic",
        vendor = {},
        vendorDocument = {},
        authRequest,
    } = usePage().props;

    const basicForm = useForm({
        upgrade,
        shop_name_en: vendor.shop_name_en || "",
        phone: vendor.phone || "",
        email: vendor.email || "",
        country: vendor.country || "",
        district: vendor.district || "",
        upozila: vendor.upozila || "",
        village: vendor.village || "",
        zip: vendor.zip || "",
        road_no: vendor.road_no || "",
        house_no: vendor.house_no || "",
        newLogo: null,
        newBanner: null,
    });

    const documentForm = useForm({
        upgrade,
        nid: vendorDocument.nid || "",
        shop_tin: vendorDocument.shop_tin || "",
        shop_trade: vendorDocument.shop_trade || "",
        nid_front: null,
        nid_back: null,
        shop_tin_image: null,
        shop_trade_image: null,
    });

    const submitBasic = (e) => {
        e.preventDefault();
        basicForm.post(route("upgrade.vendor.update", { id }), {
            forceFormData: true,
        });
    };

    const submitDocument = (e) => {
        e.preventDefault();
        documentForm.post(
            route("upgrade.vendor.updateDocument", {
                id: vendorDocument.id || 0,
            }),
            {
                forceFormData: true,
            },
        );
    };

    return (
        <UserDash>
            <div>
                <SectionSection>
                    <SectionHeader
                        title={`${upgrade.charAt(0).toUpperCase() + upgrade.slice(1)} Shop Request`}
                        content={
                            <div>
                                Edit and Upgrade Your{" "}
                                {upgrade.charAt(0).toUpperCase() + upgrade.slice(1)}{" "}
                                Request Form{" "}
                                <NavLink
                                    href={route("upgrade.vendor.index", {
                                        upgrade,
                                    })}
                                >
                                    Previous Request
                                </NavLink>
                                <br />
                                <UpgradeStatus authRequest={authRequest} />
                            </div>
                        }
                    />

                    <SectionInner>
                        <div className="flex justify-between">
                            <div>
                                <NavLink
                                    active={nav === "basic"}
                                    href={route("upgrade.vendor.edit", {
                                        id,
                                        upgrade,
                                        nav: "basic",
                                    })}
                                >
                                    Basic
                                </NavLink>
                                <NavLink
                                    active={nav === "document"}
                                    href={route("upgrade.vendor.edit", {
                                        id,
                                        upgrade,
                                        nav: "document",
                                    })}
                                >
                                    Document
                                </NavLink>
                            </div>

                            <div>
                                <NavLink
                                    href={route("upgrade.vendor.create", {
                                        upgrade,
                                    })}
                                >
                                    New Request
                                </NavLink>
                            </div>
                        </div>
                    </SectionInner>
                </SectionSection>

                {nav === "basic" && (
                    <form onSubmit={submitBasic}>
                        <SectionSection>
                            <SectionInner>
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    label="Your Shop Name"
                                    name="shop_name_en"
                                    error={basicForm.errors.shop_name_en}
                                    value={basicForm.data.shop_name_en}
                                    onChange={(e) =>
                                        basicForm.setData(
                                            "shop_name_en",
                                            e.target.value,
                                        )
                                    }
                                />

                                <InputFile
                                    label="Logo"
                                    error="newLogo"
                                    errors={basicForm.errors}
                                >
                                    <p>100x100 logo</p>
                                    <div
                                        style={{ width: "100px", height: "100px" }}
                                        className="border rounded"
                                    >
                                        {basicForm.data.newLogo ? (
                                            <img
                                                style={{
                                                    width: "100px",
                                                    height: "100px",
                                                }}
                                                className="border rounded shadow"
                                                src={URL.createObjectURL(
                                                    basicForm.data.newLogo,
                                                )}
                                                alt="100x100"
                                            />
                                        ) : (
                                            vendor.logo_url && (
                                                <img
                                                    style={{
                                                        width: "100px",
                                                        height: "100px",
                                                    }}
                                                    className="border rounded shadow"
                                                    src={vendor.logo_url}
                                                    alt="100x100"
                                                />
                                            )
                                        )}
                                    </div>
                                    <div className="relative">
                                        <TextInput
                                            type="file"
                                            id="logo"
                                            className="absolute hidden"
                                            onChange={(e) =>
                                                basicForm.setData(
                                                    "newLogo",
                                                    e.target.files[0],
                                                )
                                            }
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
                                    label="Banner"
                                    error="newBanner"
                                    errors={basicForm.errors}
                                >
                                    <p>100x300 banner image</p>
                                    <div
                                        style={{ width: "300px", height: "100px" }}
                                        className="border rounded"
                                    >
                                        {basicForm.data.newBanner ? (
                                            <img
                                                style={{
                                                    width: "300px",
                                                    height: "100px",
                                                }}
                                                className="border rounded shadow"
                                                src={URL.createObjectURL(
                                                    basicForm.data.newBanner,
                                                )}
                                                alt="100x300"
                                            />
                                        ) : (
                                            vendor.banner_url && (
                                                <img
                                                    style={{
                                                        width: "300px",
                                                        height: "100px",
                                                    }}
                                                    className="border rounded shadow"
                                                    src={vendor.banner_url}
                                                    alt="100x300"
                                                />
                                            )
                                        )}
                                    </div>
                                    <div className="relative">
                                        <TextInput
                                            type="file"
                                            id="banner"
                                            className="absolute hidden"
                                            onChange={(e) =>
                                                basicForm.setData(
                                                    "newBanner",
                                                    e.target.files[0],
                                                )
                                            }
                                        />
                                        <label
                                            htmlFor="banner"
                                            className="p-2 border rounded shadow"
                                        >
                                            <i className="fas fa-upload"></i>
                                        </label>
                                    </div>
                                </InputFile>

                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    type="number"
                                    label="Your Shop Phone"
                                    name="phone"
                                    error={basicForm.errors.phone}
                                    value={basicForm.data.phone}
                                    onChange={(e) =>
                                        basicForm.setData("phone", e.target.value)
                                    }
                                />
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    type="email"
                                    label="Your Shop email"
                                    name="email"
                                    error={basicForm.errors.email}
                                    value={basicForm.data.email}
                                    onChange={(e) =>
                                        basicForm.setData("email", e.target.value)
                                    }
                                />
                            </SectionInner>
                        </SectionSection>

                        <SectionSection>
                            <SectionInner>
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    label="Your Country"
                                    name="country"
                                    error={basicForm.errors.country}
                                    value={basicForm.data.country}
                                    onChange={(e) =>
                                        basicForm.setData(
                                            "country",
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    label="District/State"
                                    name="district"
                                    error={basicForm.errors.district}
                                    value={basicForm.data.district}
                                    onChange={(e) =>
                                        basicForm.setData(
                                            "district",
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    label="Upozila/ City"
                                    name="upozila"
                                    error={basicForm.errors.upozila}
                                    value={basicForm.data.upozila}
                                    onChange={(e) =>
                                        basicForm.setData(
                                            "upozila",
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    label="Village"
                                    name="village"
                                    error={basicForm.errors.village}
                                    value={basicForm.data.village}
                                    onChange={(e) =>
                                        basicForm.setData(
                                            "village",
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    label="Zip Code"
                                    name="zip"
                                    error={basicForm.errors.zip}
                                    value={basicForm.data.zip}
                                    onChange={(e) =>
                                        basicForm.setData("zip", e.target.value)
                                    }
                                />
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    label="Road No"
                                    name="road_no"
                                    error={basicForm.errors.road_no}
                                    value={basicForm.data.road_no}
                                    onChange={(e) =>
                                        basicForm.setData(
                                            "road_no",
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputField
                                    className="md:flex"
                                    inputClass="w-full"
                                    label="House No"
                                    name="house_no"
                                    error={basicForm.errors.house_no}
                                    value={basicForm.data.house_no}
                                    onChange={(e) =>
                                        basicForm.setData(
                                            "house_no",
                                            e.target.value,
                                        )
                                    }
                                />

                                <PrimaryButton disabled={basicForm.processing}>
                                    Submit
                                </PrimaryButton>
                            </SectionInner>
                        </SectionSection>
                    </form>
                )}

                {nav === "document" && (
                    <form onSubmit={submitDocument}>
                        <SectionSection>
                            <SectionInner>
                                <InputField
                                    className="md:flex"
                                    label="Your NID No"
                                    name="nid"
                                    error={documentForm.errors.nid}
                                    value={documentForm.data.nid}
                                    onChange={(e) =>
                                        documentForm.setData(
                                            "nid",
                                            e.target.value,
                                        )
                                    }
                                />

                                <InputFile
                                    label="Your NID Image (front side)"
                                    error="nid_front"
                                    errors={documentForm.errors}
                                >
                                    {vendorDocument.nid_front_url &&
                                        !documentForm.data.nid_front && (
                                            <img
                                                src={vendorDocument.nid_front_url}
                                                alt=""
                                            />
                                        )}
                                    {documentForm.data.nid_front && (
                                        <img
                                            src={URL.createObjectURL(
                                                documentForm.data.nid_front,
                                            )}
                                            alt=""
                                        />
                                    )}
                                    <TextInput
                                        accept="png, jpg, jpeg"
                                        type="file"
                                        onChange={(e) =>
                                            documentForm.setData(
                                                "nid_front",
                                                e.target.files[0],
                                            )
                                        }
                                    />
                                </InputFile>

                                <InputFile
                                    label="Your NID Image (back side)"
                                    error="nid_back"
                                    errors={documentForm.errors}
                                >
                                    {vendorDocument.nid_back_url &&
                                        !documentForm.data.nid_back && (
                                            <img
                                                src={vendorDocument.nid_back_url}
                                                alt=""
                                            />
                                        )}
                                    {documentForm.data.nid_back && (
                                        <img
                                            src={URL.createObjectURL(
                                                documentForm.data.nid_back,
                                            )}
                                            alt=""
                                        />
                                    )}
                                    <TextInput
                                        type="file"
                                        onChange={(e) =>
                                            documentForm.setData(
                                                "nid_back",
                                                e.target.files[0],
                                            )
                                        }
                                    />
                                </InputFile>
                                <Hr />
                                <InputFile
                                    label="Your TIN No"
                                    error="shop_tin"
                                    errors={documentForm.errors}
                                >
                                    <TextInput
                                        className="w-full"
                                        value={documentForm.data.shop_tin}
                                        onChange={(e) =>
                                            documentForm.setData(
                                                "shop_tin",
                                                e.target.value,
                                            )
                                        }
                                        type="text"
                                        name="nid"
                                        placeholder="Your Business TIN"
                                    />
                                </InputFile>

                                <InputFile
                                    label="Your TIN Image (front side)"
                                    error="shop_tin_image"
                                    errors={documentForm.errors}
                                >
                                    {vendorDocument.shop_tin_image_url &&
                                        !documentForm.data.shop_tin_image && (
                                            <img
                                                src={
                                                    vendorDocument.shop_tin_image_url
                                                }
                                                alt=""
                                            />
                                        )}
                                    {documentForm.data.shop_tin_image && (
                                        <img
                                            src={URL.createObjectURL(
                                                documentForm.data
                                                    .shop_tin_image,
                                            )}
                                            alt=""
                                        />
                                    )}
                                    <TextInput
                                        type="file"
                                        onChange={(e) =>
                                            documentForm.setData(
                                                "shop_tin_image",
                                                e.target.files[0],
                                            )
                                        }
                                    />
                                </InputFile>

                                <Hr />
                                <InputField
                                    className="md:flex"
                                    label="Your business Trade Number"
                                    name="shop_trade"
                                    error={documentForm.errors.shop_trade}
                                    value={documentForm.data.shop_trade}
                                    onChange={(e) =>
                                        documentForm.setData(
                                            "shop_trade",
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputFile
                                    label="Your Trade License Image (front side)"
                                    error="shop_trade_image"
                                    errors={documentForm.errors}
                                >
                                    {vendorDocument.shop_trade_image_url &&
                                        !documentForm.data.shop_trade_image && (
                                            <img
                                                src={
                                                    vendorDocument.shop_trade_image_url
                                                }
                                                alt=""
                                            />
                                        )}
                                    {documentForm.data.shop_trade_image && (
                                        <img
                                            src={URL.createObjectURL(
                                                documentForm.data
                                                    .shop_trade_image,
                                            )}
                                            alt=""
                                        />
                                    )}
                                    <TextInput
                                        type="file"
                                        onChange={(e) =>
                                            documentForm.setData(
                                                "shop_trade_image",
                                                e.target.files[0],
                                            )
                                        }
                                    />
                                </InputFile>

                                <Hr />
                                <PrimaryButton
                                    disabled={documentForm.processing}
                                >
                                    submit
                                </PrimaryButton>
                            </SectionInner>
                        </SectionSection>
                    </form>
                )}
            </div>
        </UserDash>
    );
}

