import { useForm, usePage } from "@inertiajs/react";
import Container from "../../../../components/dashboard/Container";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";
import Hr from "../../../../components/Hr";
import InputFile from "../../../../components/InputFile";
import InputLabel from "../../../../components/InputLabel";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import UserDash from "../../../../components/user/dash/UserDash";

export default function UpgradeRiderEdit() {
    const { rider } = usePage().props;

    const { data, setData, post, processing, errors } = useForm({
        phone: rider?.phone || "",
        email: rider?.email || "",
        nid: rider?.nid || "",
        fixed_address: rider?.fixed_address || "",
        current_address: rider?.current_address || "",
        area_condition: rider?.area_condition || "dhaka",
        targeted_area: rider?.targeted_area || "",
        nid_photo_front: null,
        nid_photo_back: null,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route("upgrade.rider.update", rider.id), {
            forceFormData: true,
        });
    };

    return (
        <UserDash>
            <Container>
                <div>
                    <SectionSection>
                        <SectionHeader
                            title="Rider request"
                            content={
                                <>
                                    Edit and Upgrade Your Vendor Request Form{" "}
                                    <NavLink
                                        className="border-b font-bold"
                                        href={route("upgrade.rider.index")}
                                    >
                                        Previous Request
                                    </NavLink>
                                </>
                            }
                        />
                    </SectionSection>

                    <form onSubmit={submit}>
                        <SectionSection>
                            <SectionInner>
                                <InputFile
                                    label="You phone No"
                                    name="phone"
                                    error="phone"
                                    errors={errors}
                                >
                                    <TextInput
                                        className="w-full"
                                        value={data.phone}
                                        onChange={(e) =>
                                            setData("phone", e.target.value)
                                        }
                                        placeholder="Your phone No "
                                    />
                                </InputFile>
                                <InputFile
                                    label="You email No"
                                    name="email"
                                    error="email"
                                    errors={errors}
                                >
                                    <TextInput
                                        type="email"
                                        className="w-full"
                                        value={data.email}
                                        onChange={(e) =>
                                            setData("email", e.target.value)
                                        }
                                        placeholder="Your email No "
                                    />
                                </InputFile>
                                <Hr />

                                <InputFile
                                    label="You NID No"
                                    name="nid"
                                    error="nid"
                                    errors={errors}
                                >
                                    <TextInput
                                        className="w-full"
                                        value={data.nid}
                                        onChange={(e) =>
                                            setData("nid", e.target.value)
                                        }
                                        placeholder="Your NID No "
                                    />
                                </InputFile>

                                <InputFile
                                    label="You NID Front Image"
                                    name="nid_photo_front"
                                    error="nid_photo_front"
                                    errors={errors}
                                >
                                    <div>
                                        <div className="flex">
                                            {data.nid_photo_front && (
                                                <img
                                                    className="mb-2"
                                                    style={{
                                                        width: "150px",
                                                        height: "100px",
                                                    }}
                                                    src={URL.createObjectURL(
                                                        data.nid_photo_front,
                                                    )}
                                                    alt=""
                                                />
                                            )}
                                            {rider?.nid_photo_front_url && (
                                                <img
                                                    className="mb-2"
                                                    style={{
                                                        width: "150px",
                                                        height: "100px",
                                                    }}
                                                    src={
                                                        rider.nid_photo_front_url
                                                    }
                                                    alt=""
                                                />
                                            )}
                                        </div>
                                        <input
                                            type="file"
                                            className="w-full"
                                            onChange={(e) =>
                                                setData(
                                                    "nid_photo_front",
                                                    e.target.files[0],
                                                )
                                            }
                                        />
                                    </div>
                                </InputFile>
                                <InputFile
                                    label="You NID Back Image"
                                    name="nid_photo_back"
                                    error="nid_photo_back"
                                    errors={errors}
                                >
                                    <div>
                                        <div className="flex">
                                            {data.nid_photo_back && (
                                                <img
                                                    className="mb-2"
                                                    style={{
                                                        width: "150px",
                                                        height: "100px",
                                                    }}
                                                    src={URL.createObjectURL(
                                                        data.nid_photo_back,
                                                    )}
                                                    alt=""
                                                />
                                            )}
                                            {rider?.nid_photo_back_url && (
                                                <img
                                                    className="mb-2"
                                                    style={{
                                                        width: "150px",
                                                        height: "100px",
                                                    }}
                                                    src={rider.nid_photo_back_url}
                                                    alt=""
                                                />
                                            )}
                                        </div>
                                        <input
                                            type="file"
                                            className="w-full"
                                            onChange={(e) =>
                                                setData(
                                                    "nid_photo_back",
                                                    e.target.files[0],
                                                )
                                            }
                                        />
                                    </div>
                                </InputFile>

                                <Hr />
                                <InputFile
                                    label="You Fixed Address"
                                    name="fixed_address"
                                    error="fixed_address"
                                    errors={errors}
                                >
                                    <textarea
                                        className="rounded-md w-full"
                                        value={data.fixed_address}
                                        onChange={(e) =>
                                            setData(
                                                "fixed_address",
                                                e.target.value,
                                            )
                                        }
                                        placeholder="Your Permanent Address based on NID "
                                    ></textarea>
                                </InputFile>
                                <InputFile
                                    label="You Current Address"
                                    name="current_address"
                                    error="current_address"
                                    errors={errors}
                                >
                                    <textarea
                                        className="rounded-md w-full"
                                        value={data.current_address}
                                        onChange={(e) =>
                                            setData(
                                                "current_address",
                                                e.target.value,
                                            )
                                        }
                                        placeholder="Your Current Address based on NID "
                                    ></textarea>
                                </InputFile>
                            </SectionInner>
                        </SectionSection>

                        <SectionSection>
                            <SectionInner>
                                <InputFile
                                    label="Chose Your Area"
                                    name="area_condition"
                                    error="area_condition"
                                    errors={errors}
                                >
                                    <div className="w-48 space-y-2">
                                        <div className="flex items-center justify-start border rounded-lg shadow-sm px-3 py-2">
                                            <TextInput
                                                style={{
                                                    width: "20px",
                                                    height: "20px",
                                                }}
                                                type="radio"
                                                name="area_condition"
                                                className="mr-3 m-0"
                                                value="dhaka"
                                                checked={
                                                    data.area_condition ===
                                                    "dhaka"
                                                }
                                                onChange={(e) =>
                                                    setData(
                                                        "area_condition",
                                                        e.target.value,
                                                    )
                                                }
                                                id="area_condition_1"
                                            />
                                            <InputLabel
                                                htmlFor="area_condition_1"
                                                className="m-0"
                                            >
                                                Inside of Dhaka
                                            </InputLabel>
                                        </div>
                                        <div className="flex items-center justify-start border rounded-lg shadow-sm px-3 py-2">
                                            <TextInput
                                                style={{
                                                    width: "20px",
                                                    height: "20px",
                                                }}
                                                type="radio"
                                                name="area_condition"
                                                className="mr-3 m-0"
                                                value="other"
                                                checked={
                                                    data.area_condition ===
                                                    "other"
                                                }
                                                onChange={(e) =>
                                                    setData(
                                                        "area_condition",
                                                        e.target.value,
                                                    )
                                                }
                                                id="area_condition_2"
                                            />
                                            <InputLabel
                                                htmlFor="area_condition_2"
                                                className="m-0"
                                            >
                                                {" "}
                                                Outside Of Dhaka{" "}
                                            </InputLabel>
                                        </div>
                                    </div>
                                </InputFile>
                                <Hr />
                                <div>
                                    <InputFile
                                        label="Targetted Area"
                                        name="targeted_area"
                                        error="targeted_area"
                                        errors={errors}
                                    >
                                        <TextInput
                                            className="w-full"
                                            value={data.targeted_area}
                                            onChange={(e) =>
                                                setData(
                                                    "targeted_area",
                                                    e.target.value,
                                                )
                                            }
                                        />
                                    </InputFile>
                                </div>
                                <Hr />
                                <PrimaryButton disabled={processing}>
                                    <i className="fas fa-sync pr-2"></i> Update
                                    & Save
                                </PrimaryButton>
                            </SectionInner>
                        </SectionSection>
                    </form>
                </div>
            </Container>
        </UserDash>
    );
}
