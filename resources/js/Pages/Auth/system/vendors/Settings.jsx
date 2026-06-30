import { useForm, usePage } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import Container from "../../../../components/dashboard/Container";
import PageHeader from "../../../../components/dashboard/PageHeader";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionHeader from "../../../../components/dashboard/section/Header";
import Hr from "../../../../components/Hr";
import InputLabel from "../../../../components/InputLabel";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import VendorNavigation from "./_VendorNavigation";

export default function Settings() {
    const { vendor } = usePage().props;

    const settingsForm = useForm({
        status: vendor?.status ?? "Pending",
        system_get_comission: vendor?.system_get_comission ?? "",
        allow_max_product_upload: vendor?.allow_max_product_upload ?? 0,
        max_product_upload: vendor?.max_product_upload ?? "",
        can_resell_products: vendor?.can_resell_products ?? 0,
    });

    const rejectionForm = useForm({
        is_rejected: vendor?.is_rejected ? 1 : 0,
        rejected_for: vendor?.rejected_for ?? "",
    });

    const submitSettings = (e) => {
        e.preventDefault();
        settingsForm.post(route("system.vendor.update", { id: vendor.id }));
    };

    const submitRejection = (e) => {
        e.preventDefault();
        rejectionForm.post(route("system.vendor.update", { id: vendor.id }));
    };

    return (
        <AppLayout
            title="Vendor Settings"
            header={
                <PageHeader>
                    <VendorNavigation
                        vendor={vendor}
                        activeRoute="system.vendor.settings"
                    />
                </PageHeader>
            }
        >
            <div>
                <Container>
                    <form onSubmit={submitSettings}>
                        <SectionSection>
                            <SectionHeader
                                title="Settings"
                                content="Set up your vendor membership status and important things."
                            />
                            <SectionInner>
                                <div className="flex w-full flex-1 flex-col gap-4 md:flex-row md:gap-10">
                                    <div className="p-3 bg-gray-100 rounded-md shadow-sm w-full">
                                        <hr />
                                        <div className="text-md border-b w-full p-3">
                                            <div className="font-bold">Vendor ID: </div>
                                            <div>{vendor?.id ?? "N/A"}</div>
                                        </div>
                                        <div className="text-md border-b w-full p-3">
                                            <div className="font-bold">Vendor Name: </div>
                                            <div>{vendor?.user?.name ?? "N/A"}</div>
                                        </div>
                                        <div className="text-md border-b w-full p-3">
                                            <div className="font-bold">Vendor Email: </div>
                                            <div>{vendor?.user?.email ?? "N/A"}</div>
                                        </div>
                                        <div className="text-md border-b w-full p-3">
                                            <div className="font-bold">Vendor Phone: </div>
                                            <div>{vendor?.user?.phone ?? "N/A"}</div>
                                        </div>
                                        <div className="text-md  w-full p-3">
                                            <div className="font-bold">Shop Name: </div>
                                            <div>{vendor?.shop_name_en ?? "N/A"}</div>
                                        </div>
                                    </div>
                                    <div className="p-3 bg-gray-100 rounded-md shadow-sm w-full">
                                        <hr />
                                        <div className="text-md border-b w-full p-3">
                                            <div className="font-bold">Shop Email: </div>
                                            <div>{vendor?.email ?? "N/A"}</div>
                                        </div>
                                        <div className="text-md border-b w-full p-3">
                                            <div className="font-bold">Shop Phone: </div>
                                            <div>{vendor?.phone ?? "N/A"}</div>
                                        </div>
                                        <div className="text-md border-b w-full p-3">
                                            <div className="font-bold">Shop Address: </div>
                                            <div>{vendor?.address ?? "N/A"}</div>
                                        </div>
                                        <div className="text-md border-b w-full p-3">
                                            <div className="font-bold">Shop Location: </div>
                                            <div>
                                                {vendor?.upazila ?? "N/A"},{" "}
                                                {vendor?.district ?? "N/A"},{" "}
                                                {vendor?.country ?? "N/A"}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className=" mt-2 p-3 border rounded bg-gray-50 shadow-sm">
                                    <div className="w-full bg-gray-50 p-3 rounded-md shadow-sm">
                                        <div>
                                            <Hr />
                                            <div className="flex flex-wrap gap-3">
                                                {["Active", "Pending", "Disabled", "Suspended"].map((status) => (
                                                    <div
                                                        key={status}
                                                        className="flex items-center rounded border border-slate-200 bg-white p-2"
                                                    >
                                                        <TextInput
                                                            type="radio"
                                                            className="m-0 mr-2"
                                                            name="status"
                                                            value={status}
                                                            checked={settingsForm.data.status === status}
                                                            onChange={(e) =>
                                                                settingsForm.setData(
                                                                    "status",
                                                                    e.target.value
                                                                )
                                                            }
                                                        />
                                                        <InputLabel className="m-0">
                                                            {status}
                                                        </InputLabel>
                                                    </div>
                                                ))}
                                            </div>
                                            {settingsForm.data.status === "Pending" ? (
                                                <div className="text-xs text-gray-500">
                                                    Pending vendor membership can update own information.
                                                </div>
                                            ) : null}
                                            <Hr />
                                            <div className="text-xs text-gray-500">
                                                <span className="font-bold">Note: </span>
                                                If you change the vendor status to "Pending" or
                                                "Disabled", then the vendor will not be able to
                                                access the dashboard.
                                                <br />
                                                If you change the vendor status to "Active", then the
                                                vendor will be able to access the dashboard and
                                                manage their products.
                                            </div>
                                            <Hr />
                                            <div className="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                                                <div className="font-medium">Comission Rate (%)</div>
                                                <div className="w-full text-xs md:w-auto">
                                                    <input
                                                        type="number"
                                                        className="form-control w-full rounded-md shoadow-sm md:w-auto"
                                                        max="100"
                                                        value={
                                                            settingsForm.data.system_get_comission
                                                        }
                                                        onChange={(e) =>
                                                            settingsForm.setData(
                                                                "system_get_comission",
                                                                e.target.value
                                                            )
                                                        }
                                                    />
                                                    <div>
                                                        You take{" "}
                                                        {vendor?.system_get_comission ?? "0"}% profit
                                                        from this vendor revinew.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="my-2 rounded bg-gray-50 border-gray-200 p-3">
                                        <div className="flex w-full flex-col gap-3 p-3 md:flex-row md:items-center md:justify-between">
                                            <div className="font-bold">
                                                Prevent adding unlimited product :
                                            </div>
                                            <div className="flex flex-wrap gap-4 md:gap-10">
                                                {[
                                                    { value: 1, label: "Yes" },
                                                    { value: 0, label: "No" },
                                                ].map((item) => (
                                                    <div
                                                        key={item.label}
                                                        className="flex items-center"
                                                    >
                                                        <input
                                                            type="radio"
                                                            value={item.value}
                                                            style={{
                                                                width: 20,
                                                                height: 20,
                                                            }}
                                                            checked={
                                                                Number(
                                                                    settingsForm.data
                                                                        .allow_max_product_upload
                                                                ) === item.value
                                                            }
                                                            onChange={(e) =>
                                                                settingsForm.setData(
                                                                    "allow_max_product_upload",
                                                                    Number(e.target.value)
                                                                )
                                                            }
                                                        />
                                                        <div className="px-2">
                                                            {item.label}
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                        <div className="flex w-full flex-col gap-2 px-3 md:flex-row md:items-center md:justify-between">
                                            <div className="font-bold">Maximum Product : </div>
                                            <div>
                                                <TextInput
                                                    type="number"
                                                    placeholder="100"
                                                    className="w-full md:w-20"
                                                    value={
                                                        settingsForm.data.max_product_upload
                                                    }
                                                    onChange={(e) =>
                                                        settingsForm.setData(
                                                            "max_product_upload",
                                                            e.target.value
                                                        )
                                                    }
                                                />
                                            </div>
                                        </div>
                                        <div className="text-xs text-gray-500 px-3">
                                            If you set the maximum product, then the vendor will
                                            not be able to upload more than this number of products.
                                        </div>
                                    </div>

                                    <div className="my-2 bg-gray-50 p-3">
                                        <div className="flex w-full flex-col gap-3 px-3 md:flex-row md:items-center md:justify-between">
                                            <div className="font-bold">
                                                Allow to resell products :
                                            </div>
                                            <div className="flex flex-wrap gap-4 md:gap-10">
                                                {[
                                                    { value: 1, label: "Yes" },
                                                    { value: 0, label: "No" },
                                                ].map((item) => (
                                                    <div
                                                        key={item.label}
                                                        className="flex items-center"
                                                    >
                                                        <input
                                                            type="radio"
                                                            value={item.value}
                                                            style={{
                                                                width: 20,
                                                                height: 20,
                                                            }}
                                                            checked={
                                                                Number(
                                                                    settingsForm.data
                                                                        .can_resell_products
                                                                ) === item.value
                                                            }
                                                            onChange={(e) =>
                                                                settingsForm.setData(
                                                                    "can_resell_products",
                                                                    Number(e.target.value)
                                                                )
                                                            }
                                                        />
                                                        <div className="px-2">
                                                            {item.label}
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>

                                        <div className="text-xs text-gray-500 px-3">
                                            If you allow the vendor to resell products, then the
                                            vendor will be able to resell products from other
                                            resellers.
                                        </div>
                                    </div>

                                    <Hr />
                                    <PrimaryButton>Update Settings</PrimaryButton>
                                </div>
                            </SectionInner>
                        </SectionSection>
                    </form>

                    <div className="flex flex-col items-start justify-between md:flex-row">
                        <SectionSection>
                            <SectionHeader
                                title="Rejection"
                                content={
                                    <>
                                        If you wish to reject the vendor membership request, <br />
                                        Follow the bellow rejection projess. <br />
                                        first check to the checkbox, the give a rejection causes
                                        message.
                                    </>
                                }
                            />
                            <SectionInner>
                                <form onSubmit={submitRejection}>
                                    <div className="mb-3 flex items-start gap-3">
                                        <TextInput
                                            type="checkbox"
                                            checked={Boolean(
                                                Number(rejectionForm.data.is_rejected)
                                            )}
                                            onChange={(e) =>
                                                rejectionForm.setData(
                                                    "is_rejected",
                                                    e.target.checked ? 1 : 0
                                                )
                                            }
                                            className="mt-1 h-6 w-6 shrink-0"
                                        />
                                        <InputLabel>Rejecte the request!</InputLabel>
                                    </div>

                                    <textarea
                                        rows="8"
                                        className="w-full rounded-md p-3"
                                        placeholder="Describe why you wish to reject .... "
                                        value={rejectionForm.data.rejected_for}
                                        onChange={(e) =>
                                            rejectionForm.setData(
                                                "rejected_for",
                                                e.target.value
                                            )
                                        }
                                    ></textarea>
                                    <Hr />

                                    <PrimaryButton>submit</PrimaryButton>
                                </form>
                            </SectionInner>
                        </SectionSection>
                    </div>
                </Container>
            </div>
        </AppLayout>
    );
}
