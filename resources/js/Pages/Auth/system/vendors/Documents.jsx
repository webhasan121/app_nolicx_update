import { useForm, usePage } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import Container from "../../../../components/dashboard/Container";
import Hr from "../../../../components/Hr";
import InputFile from "../../../../components/InputFile";
import PrimaryButton from "../../../../components/PrimaryButton";
import TextInput from "../../../../components/TextInput";
import PageHeader from "../../../../components/dashboard/PageHeader";
import SectionInner from "../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import VendorNavigation from "./_VendorNavigation";

export default function Documents() {
    const { vendor } = usePage().props;

    const deadlineForm = useForm({
        deatline: vendor?.documents?.deatline ?? "",
    });

    const submitDeadline = (e) => {
        e.preventDefault();
        deadlineForm.post(
            route("system.vendor.documents.deatline", { id: vendor.id })
        );
    };

    return (
        <AppLayout
            title="Vendor Documents"
            header={
                <PageHeader>
                    <VendorNavigation
                        vendor={vendor}
                        activeRoute="system.vendor.documents"
                    />
                </PageHeader>
            }
        >
            <div>
                <Container>
                    <SectionSection>
                        <SectionHeader
                            title="Documents"
                            content="See the listed document submitted by the user"
                        />
                        <SectionInner>
                            <InputFile
                                label="Document Submited Last Date"
                                error="deatline"
                            >
                                <div className="border px-2 rounded shadow-sm">
                                    {vendor?.documents?.deatline_formatted} -{" "}
                                    {vendor?.documents?.deatline_human}
                                </div>
                            </InputFile>
                            <Hr />
                            <form onSubmit={submitDeadline}>
                                <InputFile label="set New Date" error="deatline">
                                    <div className="flex">
                                        <TextInput
                                            type="date"
                                            className="py-1"
                                            value={deadlineForm.data.deatline}
                                            onChange={(e) =>
                                                deadlineForm.setData(
                                                    "deatline",
                                                    e.target.value
                                                )
                                            }
                                        />
                                        {deadlineForm.data.deatline ? (
                                            <PrimaryButton className="ms-2 py-1">
                                                set
                                            </PrimaryButton>
                                        ) : null}
                                    </div>
                                </InputFile>
                            </form>
                        </SectionInner>
                    </SectionSection>

                    <SectionSection>
                        <InputFile label="Nid" error="nid">
                            <TextInput
                                type="number"
                                className="form-control py-1"
                                value={vendor?.documents?.nid ?? ""}
                                label="NID No"
                                name="nid"
                                error="nid"
                                onChange={() => {}}
                            />
                        </InputFile>
                        <Hr />

                        <InputFile
                            label="NID Image (front side)"
                            error="nid_front"
                        >
                            {vendor?.documents?.nid_front_url ? (
                                <img
                                    width="300px"
                                    height="200px"
                                    src={vendor.documents.nid_front_url}
                                    alt=""
                                />
                            ) : null}
                        </InputFile>
                        <Hr />

                        <InputFile
                            label="NID Image (back side)"
                            error="nid_back"
                        >
                            {vendor?.documents?.nid_back_url ? (
                                <img
                                    width="300px"
                                    height="200px"
                                    src={vendor.documents.nid_back_url}
                                    alt=""
                                />
                            ) : null}
                        </InputFile>
                        <Hr />
                    </SectionSection>

                    <SectionSection>
                        <InputFile label="TIN No" error="tin">
                            <TextInput
                                type="text"
                                name=""
                                value={vendor?.documents?.shop_tin ?? ""}
                                id=""
                                onChange={() => {}}
                            />
                        </InputFile>
                        <Hr />

                        <InputFile label="TIN Image" error="shop_tin">
                            {vendor?.documents?.shop_tin_image_url ? (
                                <img
                                    width="300px"
                                    height="200px"
                                    src={vendor.documents.shop_tin_image_url}
                                    alt=""
                                />
                            ) : null}
                        </InputFile>
                    </SectionSection>

                    <SectionSection>
                        <InputFile label="Shop Trade" error="shop_trade">
                            <TextInput
                                type="text"
                                name=""
                                value={vendor?.documents?.shop_trade ?? ""}
                                id=""
                                onChange={() => {}}
                            />
                        </InputFile>
                        <Hr />

                        <InputFile
                            label="Trade License Image"
                            error="shop_trade_image"
                        >
                            {vendor?.documents?.shop_trade_image_url ? (
                                <img
                                    width="300px"
                                    height="200px"
                                    src={vendor.documents.shop_trade_image_url}
                                    alt=""
                                />
                            ) : null}
                        </InputFile>
                    </SectionSection>
                </Container>
            </div>
        </AppLayout>
    );
}
