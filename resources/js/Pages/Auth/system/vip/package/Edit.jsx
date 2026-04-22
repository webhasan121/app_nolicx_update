import { useForm, usePage } from "@inertiajs/react";
import { useEffect, useId, useRef, useState } from "react";
import AppLayout from "../../../../../Layouts/App";
import DangerButton from "../../../../../components/DangerButton";
import Hr from "../../../../../components/Hr";
import InputLabel from "../../../../../components/InputLabel";
import NavLink from "../../../../../components/NavLink";
import PrimaryButton from "../../../../../components/PrimaryButton";
import SecondaryButton from "../../../../../components/SecondaryButton";
import TextInput from "../../../../../components/TextInput";
import Container from "../../../../../components/dashboard/Container";
import PageHeader from "../../../../../components/dashboard/PageHeader";
import SectionHeader from "../../../../../components/dashboard/section/Header";
import SectionInner from "../../../../../components/dashboard/section/Inner";
import SectionSection from "../../../../../components/dashboard/section/Section";

export default function Edit() {
    const { package: pack, paymentOptions = [] } = usePage().props;
    const inputId = useId().replace(/:/g, "");
    const editorRef = useRef(null);
    const [trixReady, setTrixReady] = useState(
        typeof window !== "undefined" && !!window.Trix
    );
    const form = useForm({
        name: pack?.name ?? "",
        price: pack?.price ?? "",
        countdown: pack?.countdown ?? "",
        coin: pack?.coin ?? "",
        m_coin: pack?.m_coin ?? "",
        ref_owner_get_coin: pack?.ref_owner_get_coin ?? "",
        description: pack?.description ?? "",
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
        form.post(route("system.package.update", { packages: pack.id }));
    };

    return (
        <AppLayout
            title="VIP Package Update"
            header={
                <PageHeader>
                    VIP Package Update
                    <br />
                    <NavLink href={route("system.vip.index")}>
                        <i className="fa-solid fa-up-right-from-square me-2"></i>
                        Back To Packages
                    </NavLink>
                </PageHeader>
            }
        >
            <Container>
                <SectionSection>
                    <SectionHeader title="Package Basic Info" content="" />

                    <SectionInner>
                        <form onSubmit={submit}>
                            <div className="flex flex-wrap space-x-3">
                                <div className="w-md py-2 border-b">
                                    <div className="text-sm">Package Name</div>
                                    <div className="text-md">
                                        <TextInput
                                            value={form.data.name}
                                            className="py-1 border-0 w-full sahdow-0"
                                            onChange={(e) =>
                                                form.setData("name", e.target.value)
                                            }
                                        />
                                    </div>
                                </div>
                                <div className="w-md py-2 border-b">
                                    <div className="text-sm">Package Price</div>
                                    <div className="text-md">
                                        <TextInput
                                            min="10"
                                            type="number"
                                            value={form.data.price}
                                            className="py-1 border-0 w-full sahdow-0"
                                            onChange={(e) =>
                                                form.setData("price", e.target.value)
                                            }
                                        />
                                    </div>
                                </div>
                                <div className="w-md py-2 border-b">
                                    <div className="text-sm">Package Task Duration (Minute)</div>
                                    <div className="text-md">
                                        <TextInput
                                            min="1"
                                            max="60"
                                            type="number"
                                            value={form.data.countdown}
                                            className="py-1 border-0 w-full sahdow-0"
                                            onChange={(e) =>
                                                form.setData("countdown", e.target.value)
                                            }
                                        />
                                    </div>
                                </div>
                                <div className="w-md py-2 border-b">
                                    <div className="text-sm">Package Daily Coin</div>
                                    <div className="text-md">
                                        <TextInput
                                            min="1"
                                            type="number"
                                            value={form.data.coin}
                                            className="py-1 border-0 w-full sahdow-0"
                                            onChange={(e) =>
                                                form.setData("coin", e.target.value)
                                            }
                                        />
                                    </div>
                                </div>
                                <div className="w-md py-2 border-b">
                                    <div className="text-sm">Package Monthly Coin</div>
                                    <div className="text-md">
                                        <TextInput
                                            min="1"
                                            type="number"
                                            value={form.data.m_coin}
                                            className="py-1 border-0 w-full sahdow-0"
                                            onChange={(e) =>
                                                form.setData("m_coin", e.target.value)
                                            }
                                        />
                                    </div>
                                </div>
                                <div className="w-md py-2 border-b">
                                    <div className="text-sm">Referrer Coin</div>
                                    <div className="text-md">
                                        <TextInput
                                            min="1"
                                            type="number"
                                            value={form.data.ref_owner_get_coin}
                                            className="py-1 border-0 w-full sahdow-0"
                                            onChange={(e) =>
                                                form.setData(
                                                    "ref_owner_get_coin",
                                                    e.target.value
                                                )
                                            }
                                        />
                                    </div>
                                </div>
                            </div>
                            <br />
                        </form>
                    </SectionInner>
                </SectionSection>

                <SectionSection>
                    <SectionHeader
                        title={
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
                        }
                        content="Manage your package payment options"
                    />

                    <SectionInner>
                        <div className="flex flex-wrap ">
                            {form.data.paymentOptions.map((option, index) => (
                                <div
                                    key={index}
                                    className="p-2 rounded border my-2 bg-white shadow border-sky-800 space-x-2"
                                >
                                    <div className="p-0 m-0">
                                        <div className="py-2 border-b">
                                            <InputLabel
                                                className="py-1"
                                                htmlFor={`pay_type_${index}`}
                                            >
                                                Payment Method
                                            </InputLabel>
                                            <TextInput
                                                type="text"
                                                className="border-0 py-1"
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
                                            <InputLabel
                                                className="py-1"
                                                htmlFor={`pay_to_${index}`}
                                            >
                                                Payment Number
                                            </InputLabel>
                                            <TextInput
                                                type="text"
                                                className="border-0 py-1"
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
                                        <br />
                                        <DangerButton
                                            type="button"
                                            className="btn border btn-sm"
                                            onClick={() => removePaymentOption(index)}
                                        >
                                            <i className="fas fa-trash"></i>
                                        </DangerButton>
                                    </div>
                                </div>
                            ))}
                        </div>
                        <br />
                        <main>
                            {trixReady && (
                                <trix-toolbar
                                    id={`my_toolbar_${inputId}`}
                                ></trix-toolbar>
                            )}
                            <div className="more-stuff-inbetween"></div>
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
                        <br />
                        <PrimaryButton type="button" onClick={submit}>
                            Update
                        </PrimaryButton>
                    </SectionInner>
                </SectionSection>

                <SectionSection>
                    <NavLink href="">
                        <i className="fa-solid fa-up-right-from-square me-2"></i>
                        Task Statatistics
                    </NavLink>
                    <NavLink href="">
                        <i className="fa-solid fa-up-right-from-square me-2"></i>
                        VIP Users
                    </NavLink>
                    <NavLink href="">
                        <i className="fa-solid fa-up-right-from-square me-2"></i>
                        Earnings
                    </NavLink>
                    <NavLink href="">
                        <i className="fa-solid fa-up-right-from-square me-2"></i>
                    </NavLink>
                </SectionSection>
            </Container>
        </AppLayout>
    );
}
