import { useForm, usePage } from "@inertiajs/react";
import { useState } from "react";
import Container from "../../../../components/dashboard/Container";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionInner from "../../../../components/dashboard/section/Inner";
import VipCart from "../../../../components/VipCart";
import SecondaryButton from "../../../../components/SecondaryButton";
import PrimaryButton from "../../../../components/PrimaryButton";
import Modal from "../../../../components/Modal";
import InputLabel from "../../../../components/InputLabel";
import TextInput from "../../../../components/TextInput";
import UserDash from "../../../../components/user/dash/UserDash";
import Hr from "../../../../components/Hr";

export default function Checkout() {
    const { package: pkg, ownerPackage } = usePage().props;

    const [showDetails, setShowDetails] = useState(false);
    const [showPurchase, setShowPurchase] = useState(false);
    const [copiedId, setCopiedId] = useState(null);

    const { data, setData, post, processing, errors } = useForm({
        package_id: pkg.id,
        payment_by: "",
        trx: "",
        name: "",
        phone: "",
        task_type: "",
        nid: "",
        nid_front: null,
        nid_back: null,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route("user.package.purchase"));
    };

    const handleCopy = async (id, value) => {
        try {
            await navigator.clipboard.writeText(value);
            setCopiedId(id);
            setTimeout(() => setCopiedId(null), 1500);
        } catch (error) {
            console.error("Copy failed:", error);
        }
    };

    return (
        <UserDash>
            <Container>
                <SectionSection>
                    <SectionInner>
                        <div className="flex justify-start">
                            <div
                                className="mb-3 col-md-5"
                                style={{ minWidth: "250px", maxWidth: "350px" }}
                            >
                                <VipCart item={pkg} active={pkg.id} />
                            </div>

                            <div className="px-3 col-lg-7 w-100">
                                <div className="text-lg font-bold">
                                    Confirm Payment First
                                </div>

                                <div className="text-sm">
                                    Please send TK {pkg.price} to bellow number.
                                    And collect your Tansactions ID for further
                                    proccess. We need your Transactions ID to
                                    identify it's you.
                                </div>
                                <Hr />

                                {pkg.payOption.map((item) => (
                                    <div
                                        key={item.id}
                                        className="p-2 mb-1 border rounded"
                                    >
                                        <div className="uppercase">
                                            {item.pay_type}
                                        </div>
                                        <div className="flex justify-between">
                                            <div className="p-2">
                                                {item.pay_to}
                                            </div>
                                            <PrimaryButton
                                                type="button"
                                                onClick={() =>
                                                    handleCopy(
                                                        item.id,
                                                        item.pay_to,
                                                    )
                                                }
                                            >
                                                {copiedId === item.id
                                                    ? "Copied"
                                                    : "Copy"}
                                            </PrimaryButton>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div className="mt-4 text-center">
                            <SecondaryButton
                                onClick={() => setShowDetails(true)}
                            >
                                View Details
                            </SecondaryButton>
                        </div>
                    </SectionInner>
                </SectionSection>

                <SectionSection>
                    <SectionInner>
                        <div>
                            <style
                                dangerouslySetInnerHTML={{
                                    __html: `
            .vip_item_info_box{
                height: 155px;
                text-align: center;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                border-radius: 8px;
            }
            .vip_item_info_box .label {

            }
          `,
                                }}
                            />

                            <div
                                style={{
                                    display: "grid",
                                    gridTemplateColumns:
                                        "repeat(auto-fit, 155px)",
                                    gridGap: "20px",
                                    justifyContent: "center",
                                }}
                            >
                                <div className="shadow vip_item_info_box">
                                    <div>
                                        Package{" "}
                                        <i className="mx-2 fas fa-check-circle"></i>
                                    </div>
                                    <hr className="w-100" />
                                    <div
                                        style={{
                                            fontWeight: 900,
                                            fontSize: "24px",
                                        }}
                                    >
                                        {pkg.name}
                                    </div>
                                </div>

                                <div className="shadow vip_item_info_box">
                                    <div>
                                        Price{" "}
                                        <i className="mx-2 fas fa-check-circle"></i>
                                    </div>
                                    <hr className="w-100" />
                                    <div
                                        style={{
                                            fontWeight: 900,
                                            fontSize: "24px",
                                        }}
                                    >
                                        {pkg.price} TK
                                    </div>
                                </div>

                                <div className="shadow vip_item_info_box">
                                    <div>Daily TK</div>
                                    <hr className="w-100" />
                                    <div
                                        style={{
                                            fontWeight: 900,
                                            fontSize: "24px",
                                        }}
                                    >
                                        {pkg.coin}
                                    </div>
                                </div>

                                <div className="shadow vip_item_info_box">
                                    <div>
                                        Daily Time{" "}
                                        <i className="mx-2 fas fa-clock"></i>
                                    </div>
                                    <hr className="w-100" />
                                    <div
                                        style={{
                                            fontWeight: 900,
                                            fontSize: "24px",
                                        }}
                                    >
                                        {pkg.countdown} Min
                                    </div>
                                </div>
                            </div>

                            <Hr />

                            {ownerPackage && (
                                <div className="my-3 text-center">
                                    <PrimaryButton
                                        className="text-white shadow btn btn-lg bg_primary"
                                        onClick={() => setShowPurchase(true)}
                                    >
                                        Procces to Purchase
                                        <i className="mx-2 fas fa-arrow-right"></i>
                                    </PrimaryButton>
                                </div>
                            )}
                        </div>
                    </SectionInner>
                </SectionSection>

                {/* DETAILS MODAL */}
                <Modal show={showDetails} onClose={() => setShowDetails(false)}>
                    <div className="p-3 border-b">Package Description</div>
                    <div
                        className="p-3"
                        dangerouslySetInnerHTML={{
                            __html: pkg.description || "No Description Found !",
                        }}
                    />
                </Modal>

                {/* PURCHASE MODAL */}
                <Modal
                    show={showPurchase}
                    onClose={() => setShowPurchase(false)}
                >
                    <div className="p-3 border-b">Purchase Package</div>

                    <form onSubmit={submit} className="p-3">
                        {/* Payment Section */}
                        <div className="p-3 border rounded">
                            <div className="mb-3">
                                <InputLabel htmlFor="method">
                                    Payment Method
                                </InputLabel>

                                <select
                                    id="method"
                                    className={`w-full rounded ${errors.payment_by ? "is-invalid" : ""}`}
                                    value={data.payment_by}
                                    onChange={(e) =>
                                        setData("payment_by", e.target.value)
                                    }
                                >
                                    <option value="">
                                        Select a payment method
                                    </option>
                                    {pkg.payOption.map((item) => (
                                        <option
                                            key={item.id}
                                            value={item.pay_type}
                                        >
                                            {item.pay_type} - {item.pay_to}
                                        </option>
                                    ))}
                                </select>

                                {errors.payment_by && (
                                    <div className="text-xs text-red-600">
                                        {errors.payment_by}
                                    </div>
                                )}
                            </div>

                            <div className="mb-3">
                                <InputLabel htmlFor="trx">
                                    Transaction ID
                                </InputLabel>

                                <TextInput
                                    id="trx"
                                    type="text"
                                    className="w-full"
                                    value={data.trx}
                                    onChange={(e) =>
                                        setData("trx", e.target.value)
                                    }
                                    placeholder="AFASDF4574SD4S"
                                />

                                <div className="text-xs">
                                    Write down the transaction ID.
                                </div>

                                {errors.trx && (
                                    <div className="text-xs text-red-600">
                                        {errors.trx}
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* User Info */}
                        <div className="p-3 my-3 border rounded">
                            <div className="mb-3">
                                <InputLabel htmlFor="name">
                                    Your Name
                                </InputLabel>

                                <TextInput
                                    id="name"
                                    type="text"
                                    className="w-full"
                                    value={data.name}
                                    onChange={(e) =>
                                        setData("name", e.target.value)
                                    }
                                    placeholder="John Doe"
                                />

                                {errors.name && (
                                    <div className="text-xs text-red-600">
                                        {errors.name}
                                    </div>
                                )}
                            </div>

                            <div className="mb-3">
                                <InputLabel htmlFor="phone">
                                    Phone Number
                                </InputLabel>

                                <TextInput
                                    id="phone"
                                    type="text"
                                    className="w-full"
                                    value={data.phone}
                                    onChange={(e) =>
                                        setData("phone", e.target.value)
                                    }
                                    placeholder="+880123456789"
                                />

                                {errors.phone && (
                                    <div className="text-xs text-red-600">
                                        {errors.phone}
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Task Type */}
                        <div
                            className={`mb-3 p-3 rounded shadow ${
                                errors.task_type ? "border border-danger" : ""
                            }`}
                        >
                            <InputLabel className="my-2 fs-2">
                                Task Type
                            </InputLabel>

                            <div className="p-3 border rounded">
                                <div className="flex align-items-center">
                                    <input
                                        type="radio"
                                        id="daily_task"
                                        value="daily"
                                        checked={data.task_type === "daily"}
                                        onChange={(e) =>
                                            setData("task_type", e.target.value)
                                        }
                                        style={{
                                            width: "20px",
                                            height: "20px",
                                        }}
                                    />
                                    <InputLabel
                                        htmlFor="daily_task"
                                        className="pl-3 m-0"
                                    >
                                        Daily Task
                                    </InputLabel>
                                </div>
                                <div className="text-xs">
                                    Daily task may be completed within 24 hours.
                                </div>
                            </div>

                            <hr />

                            <div className="p-3 border rounded">
                                <div className="flex align-items-center">
                                    <input
                                        type="radio"
                                        id="monthly_task"
                                        value="monthly"
                                        checked={data.task_type === "monthly"}
                                        onChange={(e) =>
                                            setData("task_type", e.target.value)
                                        }
                                        style={{
                                            width: "20px",
                                            height: "20px",
                                        }}
                                    />
                                    <InputLabel
                                        htmlFor="monthly_task"
                                        className="pl-3 m-0"
                                    >
                                        Monthly Task
                                    </InputLabel>
                                </div>
                                <div className="text-xs">
                                    Monthly task may be completed once per
                                    month.
                                </div>
                            </div>
                        </div>

                        {errors.task_type && (
                            <div className="text-xs text-red-600">
                                {errors.task_type}
                            </div>
                        )}

                        {/* NID Section */}
                        <div className="p-3 border">
                            <div className="mb-3">
                                <InputLabel htmlFor="nid">
                                    Your NID Number
                                </InputLabel>

                                <TextInput
                                    id="nid"
                                    type="number"
                                    className="w-full"
                                    value={data.nid}
                                    onChange={(e) =>
                                        setData("nid", e.target.value)
                                    }
                                />

                                {errors.nid && (
                                    <div className="text-xs text-red-600">
                                        {errors.nid}
                                    </div>
                                )}
                            </div>

                            <Hr />

                            <div className="row">
                                {/* Front NID */}
                                <div className="col-lg-6">
                                    <div>Front Side of NID</div>

                                    {data.nid_front && (
                                        <img
                                            src={URL.createObjectURL(
                                                data.nid_front,
                                            )}
                                            className="object-contain w-24 h-24 my-2"
                                            alt="NID Front"
                                        />
                                    )}

                                    <TextInput
                                        type="file"
                                        className="form-control"
                                        onChange={(e) =>
                                            setData(
                                                "nid_front",
                                                e.target.files[0],
                                            )
                                        }
                                    />

                                    {errors.nid_front && (
                                        <div className="text-xs text-red-600">
                                            {errors.nid_front}
                                        </div>
                                    )}
                                </div>

                                {/* Back NID */}
                                <div className="col-lg-6">
                                    <div>Back Side of NID</div>

                                    {data.nid_back && (
                                        <img
                                            src={URL.createObjectURL(
                                                data.nid_back,
                                            )}
                                            className="object-contain w-24 h-24 my-2"
                                            alt="NID Back"
                                        />
                                    )}

                                    <TextInput
                                        type="file"
                                        className="form-control"
                                        onChange={(e) =>
                                            setData(
                                                "nid_back",
                                                e.target.files[0],
                                            )
                                        }
                                    />

                                    {errors.nid_back && (
                                        <div className="text-xs text-red-600">
                                            {errors.nid_back}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Submit */}
                        <div className="mt-4 text-right">
                            <PrimaryButton disabled={processing}>
                                Confirm{" "}
                                <i className="mx-2 fas fa-arrow-right"></i>
                            </PrimaryButton>
                        </div>
                    </form>
                </Modal>
            </Container>
        </UserDash>
    );
}
