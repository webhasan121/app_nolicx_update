import { useForm, usePage } from "@inertiajs/react";
import { CKEditor } from "@ckeditor/ckeditor5-react";
import {
    ClassicEditor,
    Bold,
    Essentials,
    Heading,
    Italic,
    Link,
    List,
    Paragraph,
} from "ckeditor5";
import AppLayout from "../../../../../Layouts/App";
import DangerButton from "../../../../../components/DangerButton";
import Hr from "../../../../../components/Hr";
import InputField from "../../../../../components/InputField";
import InputLabel from "../../../../../components/InputLabel";
import NavLink from "../../../../../components/NavLink";
import PrimaryButton from "../../../../../components/PrimaryButton";
import SecondaryButton from "../../../../../components/SecondaryButton";
import Container from "../../../../../components/dashboard/Container";
import PageHeader from "../../../../../components/dashboard/PageHeader";
import SectionHeader from "../../../../../components/dashboard/section/Header";
import SectionInner from "../../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../../components/dashboard/section/Section";
import "ckeditor5/ckeditor5.css";

export default function Create() {
    const { paymentOptions = [] } = usePage().props;
    const form = useForm({
        name: "",
        price: "",
        coin: "",
        m_coin: "",
        countdown: "",
        ref_owner_get_coin: "",
        owner_get_coin: "",
        description: "",
        paymentOptions,
    });

    const addPaymentOption = () => {
        form.setData("paymentOptions", [
            ...form.data.paymentOptions,
            { pay_type: "", pay_to: "" },
        ]);
    };

    const removePaymentOption = (index) => {
        form.setData(
            "paymentOptions",
            form.data.paymentOptions.filter((_, i) => i !== index)
        );
    };

    const updatePaymentOption = (index, key, value) => {
        form.setData(
            "paymentOptions",
            form.data.paymentOptions.map((item, i) =>
                i === index ? { ...item, [key]: value } : item
            )
        );
    };

    const submit = (e) => {
        e.preventDefault();
        form.post(route("system.vip.store"));
    };

    return (
        <AppLayout
            title="VIP"
            header={
                <PageHeader>
                    VIP
                    <br />
                    <div>
                        <NavLink href={route("system.vip.index")} active={route().current("system.vip.*")}>
                            Package
                        </NavLink>
                        <NavLink href={route("system.vip.users")} active={route().current("system.vip.")}>
                            User
                        </NavLink>
                    </div>
                </PageHeader>
            }
        >
            <Container>
                <SectionSection>
                    <SectionHeader
                        title="Add VIP Package"
                        content="add more vip package to your system with specific condition."
                    />

                    <SectionInner>
                        <form onSubmit={submit}>
                            <div>
                                <InputField
                                    label="Package Name"
                                    name="name"
                                    className="md:flex"
                                    inputClass="w-full"
                                    error={form.errors.name}
                                    value={form.data.name}
                                    onChange={(e) => form.setData("name", e.target.value)}
                                />

                                <div className="md:flex">
                                    <InputField
                                        label="Package Price"
                                        name="price"
                                        type="number"
                                        error={form.errors.price}
                                        value={form.data.price}
                                        onChange={(e) => form.setData("price", e.target.value)}
                                    />
                                    <InputField
                                        label="Duration (Minute)"
                                        name="countdown"
                                        error={form.errors.countdown}
                                        value={form.data.countdown}
                                        onChange={(e) => form.setData("countdown", e.target.value)}
                                    />
                                </div>
                                <div className="md:flex">
                                    <InputField
                                        label="Daily Reward"
                                        name="coin"
                                        error={form.errors.coin}
                                        value={form.data.coin}
                                        onChange={(e) => form.setData("coin", e.target.value)}
                                    />
                                    <InputField
                                        label="Monthly Reward"
                                        name="m_coin"
                                        error={form.errors.m_coin}
                                        value={form.data.m_coin}
                                        onChange={(e) => form.setData("m_coin", e.target.value)}
                                    />
                                </div>
                                <Hr />
                                <div className="md:flex">
                                    <InputField
                                        label="By Referred Reward"
                                        name="ref_owner_get_coin"
                                        error={form.errors.ref_owner_get_coin}
                                        value={form.data.ref_owner_get_coin}
                                        onChange={(e) =>
                                            form.setData("ref_owner_get_coin", e.target.value)
                                        }
                                    />
                                </div>
                                <Hr />
                            </div>

                            <div className="p-0 my-4 mx-0 border p-2">
                                <div className="flex justify-between items-center">
                                    <h4>Payment Option</h4>
                                    <SecondaryButton
                                        type="button"
                                        onClick={addPaymentOption}
                                        className="btn btn-sm btn-info"
                                    >
                                        <i className="fas fa-plus"></i>
                                    </SecondaryButton>
                                </div>

                                <div className="paymentDiv">
                                    {form.data.paymentOptions.map((option, index) => (
                                        <div
                                            key={index}
                                            className="p-2 rounded border my-2 bg-white"
                                        >
                                            <div className="md:flex p-0 m-0">
                                                <div>
                                                    <label
                                                        className="py-1"
                                                        htmlFor={`pay_type_${index}`}
                                                    >
                                                        Payment Method
                                                    </label>
                                                    <input
                                                        type="text"
                                                        className="form-control"
                                                        placeholder="Payment Method"
                                                        value={option.pay_type}
                                                        onChange={(e) =>
                                                            updatePaymentOption(
                                                                index,
                                                                "pay_type",
                                                                e.target.value
                                                            )
                                                        }
                                                        id={`pay_type_${index}`}
                                                    />
                                                </div>
                                                <div>
                                                    <label
                                                        className="py-1"
                                                        htmlFor={`pay_to_${index}`}
                                                    >
                                                        Payment Number/AC
                                                    </label>
                                                    <input
                                                        type="text"
                                                        className="form-control"
                                                        placeholder="Payment To"
                                                        value={option.pay_to}
                                                        onChange={(e) =>
                                                            updatePaymentOption(
                                                                index,
                                                                "pay_to",
                                                                e.target.value
                                                            )
                                                        }
                                                        id={`pay_to_${index}`}
                                                    />
                                                </div>

                                                <DangerButton
                                                    type="button"
                                                    className="btn border btn-sm"
                                                    onClick={() =>
                                                        removePaymentOption(index)
                                                    }
                                                >
                                                    <i className="fas fa-trash"></i>
                                                </DangerButton>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            <Hr />

                            <div className="p-2 bg-white rounded">
                                <InputLabel className="py-1" htmlFor="desciption">
                                    Description
                                </InputLabel>
                                <main>
                                    <CKEditor
                                        editor={ClassicEditor}
                                        config={{
                                            licenseKey: "GPL",
                                            plugins: [
                                                Essentials,
                                                Paragraph,
                                                Heading,
                                                Bold,
                                                Italic,
                                                Link,
                                                List,
                                            ],
                                            toolbar: [
                                                "undo",
                                                "redo",
                                                "|",
                                                "heading",
                                                "|",
                                                "bold",
                                                "italic",
                                                "|",
                                                "link",
                                                "bulletedList",
                                                "numberedList",
                                            ],
                                        }}
                                        data={form.data.description}
                                        onChange={(_, editor) => {
                                            form.setData(
                                                "description",
                                                editor.getData()
                                            );
                                        }}
                                    />
                                </main>
                            </div>

                            <Hr />
                            <PrimaryButton>save</PrimaryButton>
                        </form>
                    </SectionInner>
                </SectionSection>
            </Container>
        </AppLayout>
    );
}
