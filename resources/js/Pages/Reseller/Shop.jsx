import { Head, useForm, usePage } from "@inertiajs/react";
import { useEffect, useId, useRef, useState } from "react";
import AppLayout from "../../Layouts/App";
import Hr from "../../components/Hr";
import PrimaryButton from "../../components/PrimaryButton";
import Container from "../../components/dashboard/Container";

export default function Shop({ shop = {} }) {
    const { auth } = usePage().props;
    const inputId = useId().replace(/:/g, "");
    const editorRef = useRef(null);
    const [trixReady, setTrixReady] = useState(typeof window !== "undefined" && !!window.Trix);
    const [logoPreview, setLogoPreview] = useState(null);
    const [bannerPreview, setBannerPreview] = useState(null);

    const form = useForm({
        id: shop?.id ?? "",
        shop_name_en: shop?.shop_name_en ?? "",
        email: shop?.email ?? "",
        phone: shop?.phone ?? "",
        address: shop?.address ?? "",
        district: shop?.district ?? "",
        upozila: shop?.upozila ?? "",
        village: shop?.village ?? "",
        zip: shop?.zip ?? "",
        road_no: shop?.road_no ?? "",
        system_get_comission: shop?.system_get_comission ?? "",
        max_product_upload: shop?.max_product_upload ?? "",
        max_resell_product: shop?.max_resell_product ?? "",
        logo: shop?.logo ?? "",
        banner: shop?.banner ?? "",
        description: shop?.description ?? "",
        newLogo: null,
        newBanner: null,
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

    useEffect(() => {
        if (!form.data.newLogo) {
            setLogoPreview(null);
            return undefined;
        }

        const url = URL.createObjectURL(form.data.newLogo);
        setLogoPreview(url);
        return () => URL.revokeObjectURL(url);
    }, [form.data.newLogo]);

    useEffect(() => {
        if (!form.data.newBanner) {
            setBannerPreview(null);
            return undefined;
        }

        const url = URL.createObjectURL(form.data.newBanner);
        setBannerPreview(url);
        return () => URL.revokeObjectURL(url);
    }, [form.data.newBanner]);

    const updateInfo = (e) => {
        e.preventDefault();
        form.post(route("my-shop.update", { user: auth?.user?.id ?? "me" }), {
            forceFormData: true,
        });
    };

    const logoSrc = logoPreview || shop?.logo_url || (shop?.logo ? `/storage/${shop.logo}` : "");
    const bannerSrc = bannerPreview || shop?.banner_url || (shop?.banner ? `/storage/${shop.banner}` : "");

    return (
        <AppLayout title="Shop">
            <Head title="Shop" />

            <Container>
                <div className="relative">
                    <div className="absolute top-0 left-0 m-2 bg-white rounded-full">
                        {logoSrc ? (
                            <img
                                className="rounded-full"
                                style={{ height: 80, width: 80 }}
                                src={logoSrc}
                                alt=""
                            />
                        ) : null}
                        <input
                            type="file"
                            id="logo"
                            className="absolute hidden"
                            onChange={(e) => form.setData("newLogo", e.target.files?.[0] ?? null)}
                        />
                        <label
                            htmlFor="logo"
                            className="absolute bottom-0 right-0 flex items-center justify-center w-6 h-6 p-1 bg-white border rounded-full"
                        >
                            <i className="fas fa-upload"></i>
                        </label>
                    </div>
                    {bannerSrc ? (
                        <img className="w-full h-48 bg-indigo-900 rounded" src={bannerSrc} alt="" />
                    ) : (
                        <div className="w-full h-48 bg-indigo-900 rounded" />
                    )}
                    <input
                        type="file"
                        id="banner"
                        className="absolute hidden"
                        onChange={(e) => form.setData("newBanner", e.target.files?.[0] ?? null)}
                    />
                    <label
                        htmlFor="banner"
                        className="absolute bottom-0 right-0 flex items-center justify-center w-6 h-6 p-1 bg-white border rounded-full"
                    >
                        <i className="fas fa-upload"></i>
                    </label>
                </div>
                <Hr />

                <main>
                    {trixReady && <trix-toolbar id={`my_toolbar_${inputId}`}></trix-toolbar>}
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
                            className="w-full border-gray-300 rounded-md shadow-sm"
                            rows="8"
                            value={form.data.description}
                            onChange={(e) => form.setData("description", e.target.value)}
                        />
                    )}
                </main>

                <div>
                    <div className="flex-1 w-full gap-10 md:flex">
                        <div className="w-full bg-white rounded-md shadow-sm">
                            <hr />
                            <div className="w-full p-3 border-b text-md">
                                <div className="font-bold">Shop ID: </div>
                                <div>{form.data.id ?? "N/A"}</div>
                            </div>
                            <div className="w-full p-3 border-b text-md">
                                <div className="font-bold">Shop Owner Name: </div>
                                <div>{auth?.user?.name ?? "N/A"}</div>
                            </div>
                            <div className="w-full p-3 border-b text-md">
                                <div className="font-bold">Shop Owner Email: </div>
                                <div>{auth?.user?.email ?? "N/A"}</div>
                            </div>
                            <div className="w-full p-3 border-b text-md">
                                <div className="font-bold">Shop Owner Phone: </div>
                                <div>{auth?.user?.phone ?? "N/A"}</div>
                            </div>
                            <div className="w-full p-3 border-b text-md">
                                <div className="font-bold">Shop Comission (%) : </div>
                                <div>{form.data.system_get_comission ?? "N/A"}</div>
                            </div>
                            <div className="w-full p-3 border-b text-md">
                                <div className="font-bold">Product Upload Capability : </div>
                                <div>{form.data.max_product_upload ?? "N/A"}</div>
                            </div>
                            <div className="w-full p-3 border-b text-md">
                                <div className="font-bold">Product Resel Capability : </div>
                                <div>{form.data.max_resell_product ?? "N/A"}</div>
                            </div>
                        </div>

                        <div className="w-full p-3 bg-white rounded-md shadow-sm">
                            <hr />

                            <div className="w-full p-3 border-b text-md">
                                <div className="font-bold">Shop: </div>
                                <input
                                    type="text"
                                    className="w-full rounded-md ring-0"
                                    value={form.data.shop_name_en}
                                    onChange={(e) => form.setData("shop_name_en", e.target.value)}
                                />
                            </div>

                            <div className="w-full p-3 border-b text-md">
                                <div className="font-bold">Shop Email: </div>
                                <input
                                    type="text"
                                    className="w-full rounded-md ring-0"
                                    value={form.data.email}
                                    onChange={(e) => form.setData("email", e.target.value)}
                                />
                            </div>
                            <div className="w-full p-3 border-b text-md">
                                <div className="font-bold">Shop Phone: </div>
                                <input
                                    type="text"
                                    className="w-full rounded-md ring-0"
                                    value={form.data.phone}
                                    onChange={(e) => form.setData("phone", e.target.value)}
                                />
                            </div>
                            <div className="w-full p-3 border-b text-md">
                                <div className="font-bold">Shop Address: </div>
                                <input
                                    type="text"
                                    className="w-full rounded-md ring-0"
                                    value={form.data.address}
                                    onChange={(e) => form.setData("address", e.target.value)}
                                />
                            </div>
                            <div className="w-full p-3 space-y-2 border-b text-md">
                                <div className="font-bold">Shop Location: </div>
                                <div className="my-1">
                                    <label className="my-1" htmlFor="dis">
                                        District
                                    </label>
                                    <input
                                        type="text"
                                        id="dis"
                                        className="w-full rounded-md ring-0"
                                        placeholder="district"
                                        value={form.data.district}
                                        onChange={(e) => form.setData("district", e.target.value)}
                                    />
                                </div>

                                <div className="my-1">
                                    <label className="my-1" htmlFor="up">
                                        Upozila
                                    </label>
                                    <input
                                        type="text"
                                        id="up"
                                        className="w-full rounded-md ring-0"
                                        placeholder="upozila"
                                        value={form.data.upozila}
                                        onChange={(e) => form.setData("upozila", e.target.value)}
                                    />
                                </div>

                                <div className="my-1">
                                    <label className="my-1" htmlFor="vil">
                                        Village
                                    </label>
                                    <input
                                        type="text"
                                        id="vil"
                                        className="w-full rounded-md ring-0"
                                        placeholder="village"
                                        value={form.data.village}
                                        onChange={(e) => form.setData("village", e.target.value)}
                                    />
                                </div>

                                <div className="my-1">
                                    <label className="my-1" htmlFor="zip">
                                        Zip
                                    </label>
                                    <input
                                        type="text"
                                        id="zip"
                                        className="w-full rounded-md ring-0"
                                        placeholder="zip"
                                        value={form.data.zip}
                                        onChange={(e) => form.setData("zip", e.target.value)}
                                    />
                                </div>

                                <div className="my-1">
                                    <input
                                        type="text"
                                        className="w-full rounded-md ring-0"
                                        placeholder="road no"
                                        value={form.data.road_no}
                                        onChange={(e) => form.setData("road_no", e.target.value)}
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <br />

                <PrimaryButton
                    className="flex justify-center w-full text-center"
                    onClick={updateInfo}
                    disabled={form.processing}
                >
                    update
                </PrimaryButton>
            </Container>
        </AppLayout>
    );
}
