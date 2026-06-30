import { Head, useForm, usePage } from "@inertiajs/react";
import { useEffect, useId, useRef, useState } from "react";
import AppLayout from "../../../../../Layouts/App";
import DangerButton from "../../../../../components/DangerButton";
import Hr from "../../../../../components/Hr";
import InputField from "../../../../../components/InputField";
import InputLabel from "../../../../../components/InputLabel";
import TextInput from "../../../../../components/TextInput";
import NavLink from "../../../../../components/NavLink";
import PrimaryButton from "../../../../../components/PrimaryButton";
import SecondaryButton from "../../../../../components/SecondaryButton";
import Container from "../../../../../components/dashboard/Container";
import PageHeader from "../../../../../components/dashboard/PageHeader";
import SectionHeader from "../../../../../components/dashboard/section/Header";
import SectionInner from "../../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../../components/dashboard/section/Section";
import useTranslation from "../../../../../hooks/useTranslation";

export default function Create() {
    const { t } = useTranslation();
    const { paymentOptions = [] } = usePage().props;
    const inputId = useId().replace(/:/g, "");
    const editorRef = useRef(null);
    const [trixReady, setTrixReady] = useState(
        typeof window !== "undefined" && !!window.Trix
    );
    const form = useForm({
        name: "",
        price: "",
        coin: "",
        m_coin: "",
        countdown: "",
        ref_owner_get_coin: "",
        owner_get_coin: "",
        image: null,
        description: "",
        paymentOptions,
    });

    useEffect(() => {
        let isMounted = true;

        if (typeof window === "undefined" || window.Trix) {
            setTrixReady(true);
            return undefined;
        }

        if (!document.querySelector('link[data-trix="true"]')) {
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.type = "text/css";
            link.href = "https://unpkg.com/trix@2.0.8/dist/trix.css";
            link.dataset.trix = "true";
            document.head.appendChild(link);
        }

        let script = document.querySelector('script[data-trix="true"]');
        const onLoad = () => {
            if (isMounted) {
                setTrixReady(true);
            }
        };

        if (!script) {
            script = document.createElement("script");
            script.src = "https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js";
            script.async = true;
            script.dataset.trix = "true";
            script.addEventListener("load", onLoad);
            document.body.appendChild(script);
        } else if (window.Trix) {
            setTrixReady(true);
        } else {
            script.addEventListener("load", onLoad);
        }

        return () => {
            isMounted = false;
            if (script) {
                script.removeEventListener("load", onLoad);
            }
        };
    }, []);

    useEffect(() => {
        const editor = editorRef.current;

        if (!editor || !trixReady) {
            return undefined;
        }

        const handleChange = (event) => {
            form.setData("description", event.target.value);
        };

        editor.addEventListener("trix-change", handleChange);

        if (form.data.description && editor.editor) {
            editor.editor.loadHTML(form.data.description);
        }

        return () => {
            editor.removeEventListener("trix-change", handleChange);
        };
    }, [trixReady]);

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
        form.post(route("system.vip.store"), {
            forceFormData: true,
        });
    };

    return (
        <AppLayout
            title={t("VIP")}
            header={
                <PageHeader>
                    <div className="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div>{t("VIP")}</div>
                        <div className="flex flex-wrap items-center gap-3">
                        <NavLink
                            href={route("system.vip.index")}
                            active={
                                route().current("system.vip.index") ||
                                route().current("system.vip.crate") ||
                                route().current("system.package.edit")
                            }
                        >{t("Package")}</NavLink>
                        <NavLink
                            href={route("system.vip.users")}
                            active={route().current("system.vip.users")}
                        >{t("User")}</NavLink>
                        </div>
                    </div>
                </PageHeader>
            }
        >
            <Head title={t("Add VIP Package")} />
            <Container>
                <SectionSection>
                    <SectionHeader
                        title={t("Add VIP Package")}
                        content={t("add more vip package to your system with specific condition.")}
                    />

                    <SectionInner>
                        <form onSubmit={submit}>
                            <div>
                                <InputField
                                    label={t("Package Name")}
                                    name="name"
                                    inputClass="w-full"
                                    error={form.errors.name}
                                    value={form.data.name}
                                    onChange={(e) => form.setData("name", e.target.value)}
                                />

                                <div className="p-2 my-3 bg-white border rounded">
                                    <InputLabel className="py-1" htmlFor="package_image">
                                        {t("Package Image")}
                                    </InputLabel>
                                    {form.data.image && (
                                        <img
                                            src={URL.createObjectURL(form.data.image)}
                                            alt="Package preview"
                                            className="object-contain w-32 h-24 mb-2 border rounded"
                                        />
                                    )}
                                    <TextInput
                                        id="package_image"
                                        type="file"
                                        accept="image/*"
                                        className="w-full"
                                        onChange={(e) =>
                                            form.setData("image", e.target.files[0] ?? null)
                                        }
                                    />
                                    {form.errors.image && (
                                        <div className="text-xs text-red-600">
                                            {form.errors.image}
                                        </div>
                                    )}
                                </div>

                                <div className="grid gap-3 lg:grid-cols-2">
                                    <InputField
                                        label={t("Package Price")}
                                        name="price"
                                        type="number"
                                        error={form.errors.price}
                                        value={form.data.price}
                                        onChange={(e) => form.setData("price", e.target.value)}
                                    />
                                    <InputField
                                        label={t("Duration (Minute)")}
                                        name="countdown"
                                        error={form.errors.countdown}
                                        value={form.data.countdown}
                                        onChange={(e) => form.setData("countdown", e.target.value)}
                                    />
                                </div>
                                <div className="grid gap-3 lg:grid-cols-2">
                                    <InputField
                                        label={t("Daily Reward")}
                                        name="coin"
                                        error={form.errors.coin}
                                        value={form.data.coin}
                                        onChange={(e) => form.setData("coin", e.target.value)}
                                    />
                                    <InputField
                                        label={t("Monthly Reward")}
                                        name="m_coin"
                                        error={form.errors.m_coin}
                                        value={form.data.m_coin}
                                        onChange={(e) => form.setData("m_coin", e.target.value)}
                                    />
                                </div>
                                <Hr />
                                <div className="grid gap-3 lg:grid-cols-2">
                                    <InputField
                                        label={t("By Referred Reward")}
                                        name="ref_owner_get_coin"
                                        error={form.errors.ref_owner_get_coin}
                                        value={form.data.ref_owner_get_coin}
                                        onChange={(e) =>
                                            form.setData("ref_owner_get_coin", e.target.value)
                                        }
                                    />
                                    <InputField
                                        label={t("Owner Reward")}
                                        name="owner_get_coin"
                                        error={form.errors.owner_get_coin}
                                        value={form.data.owner_get_coin}
                                        onChange={(e) =>
                                            form.setData("owner_get_coin", e.target.value)
                                        }
                                    />
                                </div>
                                <Hr />
                            </div>

                            <div className="my-4 rounded border bg-white p-3 sm:p-4">
                                <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <h4>{t("Payment Option")}</h4>
                                    <SecondaryButton
                                        type="button"
                                        onClick={addPaymentOption}
                                        className="inline-flex h-10 w-10 self-start justify-center px-0"
                                    >
                                        <i className="fas fa-plus"></i>
                                    </SecondaryButton>
                                </div>

                                <div className="paymentDiv">
                                    {form.data.paymentOptions.map((option, index) => (
                                        <div
                                            key={index}
                                            className="my-3 rounded border bg-slate-50 p-3"
                                        >
                                            <div className="grid gap-3 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto] md:items-end">
                                                <div className="min-w-0">
                                                    <label
                                                        className="py-1 text-sm font-medium text-gray-700"
                                                        htmlFor={`pay_type_${index}`}
                                                    >{t("Payment Method")}</label>
                                                    <TextInput
                                                        type="text"
                                                        className="w-full"
                                                        placeholder={t("Payment Method")}
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
                                                <div className="min-w-0">
                                                    <label
                                                        className="py-1 text-sm font-medium text-gray-700"
                                                        htmlFor={`pay_to_${index}`}
                                                    >{t("Payment Number/AC")}</label>
                                                    <TextInput
                                                        type="text"
                                                        className="w-full"
                                                        placeholder={t("Payment To")}
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
                                                    className="inline-flex h-10 w-10 justify-center self-start justify-self-start px-0 md:self-end md:justify-self-end"
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
                                <main className="min-w-0">
                                    {trixReady && (
                                        <div className="overflow-x-auto">
                                            <trix-toolbar
                                                id={`my_toolbar_${inputId}`}
                                            ></trix-toolbar>
                                        </div>
                                    )}
                                    <input
                                        type="hidden"
                                        name="content"
                                        id={`my_input_${inputId}`}
                                        value={form.data.description}
                                        onChange={() => {}}
                                    />
                                    {trixReady ? (
                                        <trix-editor
                                            ref={editorRef}
                                            toolbar={`my_toolbar_${inputId}`}
                                            input={`my_input_${inputId}`}
                                        ></trix-editor>
                                    ) : (
                                        <textarea
                                            className="w-full rounded-md shadow-sm border-gray-300"
                                            rows="10"
                                            value={form.data.description}
                                            onChange={(e) =>
                                                form.setData("description", e.target.value)
                                            }
                                        />
                                    )}
                                </main>
                            </div>

                            <Hr />
                            <PrimaryButton className="w-full justify-center sm:w-auto">
                                {t("save")}
                            </PrimaryButton>
                        </form>
                    </SectionInner>
                </SectionSection>
            </Container>
        </AppLayout>
    );
}
