import { Head, useForm } from "@inertiajs/react";
import { useEffect, useId, useRef, useState } from "react";
import AppLayout from "../../../Layouts/App";
import Hr from "../../../components/Hr";
import InputField from "../../../components/InputField";
import InputFile from "../../../components/InputFile";
import PrimaryButton from "../../../components/PrimaryButton";
import ProductAttributesInput from "../../../components/ProductAttributesInput";
import CategorySelect from "../../../components/CategorySelect";
import Container from "../../../components/dashboard/Container";
import PageHeader from "../../../components/dashboard/PageHeader";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import {
    PRODUCT_VIDEO_DURATION_ERROR,
    validateProductVideoDuration,
} from "../../../utils/videoValidation";
import useTranslation from "../../../hooks/useTranslation";

const MAX_OTHER_IMAGES = 8;

export default function Create({ categories = [], shop, ableToCreate = true, requiresShop = false }) {
    const { t } = useTranslation();
    const inputId = useId().replace(/:/g, "");
    const editorRef = useRef(null);
    const [trixReady, setTrixReady] = useState(typeof window !== "undefined" && !!window.Trix);

    const form = useForm({
        name: "",
        title: "",
        category_id: "",
        buying_price: "",
        price: "",
        unit: "",
        offer_type: false,
        discount: "",
        display_at_home: false,
        cod: false,
        courier: false,
        hand: false,
        shipping_in_dhaka: "",
        shipping_out_dhaka: "",
        shipping_note: "",
        description: "This is description",
        meta_keyword: "",
        meta_title: "",
        meta_tags: "",
        meta_description: "",
        meta_thumbnail: null,
        thumb: null,
        video: "",
        newImage: [],
        attr_name: "",
        attr_value: "",
        attributes: [{ name: "", value: "" }],
    });

    const [thumbPreview, setThumbPreview] = useState(null);
    const [metaThumbPreview, setMetaThumbPreview] = useState(null);
    const [newImagePreviews, setNewImagePreviews] = useState([]);

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
        if (!(form.data.thumb instanceof File)) {
            setThumbPreview(null);
            return undefined;
        }
        const url = URL.createObjectURL(form.data.thumb);
        setThumbPreview(url);
        return () => URL.revokeObjectURL(url);
    }, [form.data.thumb]);

    useEffect(() => {
        if (!(form.data.meta_thumbnail instanceof File)) {
            setMetaThumbPreview(null);
            return undefined;
        }
        const url = URL.createObjectURL(form.data.meta_thumbnail);
        setMetaThumbPreview(url);
        return () => URL.revokeObjectURL(url);
    }, [form.data.meta_thumbnail]);

    useEffect(() => {
        if (!Array.isArray(form.data.newImage) || form.data.newImage.length === 0) {
            setNewImagePreviews([]);
            return undefined;
        }

        const urls = form.data.newImage.map((file) => URL.createObjectURL(file));
        setNewImagePreviews(urls);

        return () => {
            urls.forEach((url) => URL.revokeObjectURL(url));
        };
    }, [form.data.newImage]);

    const handleVideoChange = async (event) => {
        const file = event.target.files?.[0] ?? null;

        if (!file) {
            form.clearErrors("video");
            form.setData("video", null);
            return;
        }

        try {
            await validateProductVideoDuration(file);
            form.clearErrors("video");
            form.setData("video", file);
        } catch (error) {
            event.target.value = "";
            form.setData("video", null);
            form.setError("video", error?.message || PRODUCT_VIDEO_DURATION_ERROR);
        }
    };

    const submit = (e) => {
        e.preventDefault();
        form.post(route("vendor.products.store"), {
            forceFormData: true,
        });
    };

    const handleOtherImagesChange = (event) => {
        const currentFiles = Array.isArray(form.data.newImage)
            ? form.data.newImage
            : [];
        const selectedFiles = Array.from(event.target.files ?? []);
        const nextFiles = [...currentFiles, ...selectedFiles];

        if (nextFiles.length > MAX_OTHER_IMAGES) {
            window.alert(`You can upload a maximum of ${MAX_OTHER_IMAGES} other images.`);
            event.target.value = "";
            return;
        }

        form.setData("newImage", nextFiles);
        event.target.value = "";
    };

    return (
        <AppLayout title={t("Add Products")} header={<PageHeader>{t("Add Products")}</PageHeader>}>
            <Head title={t("Add Products")} />

            <Container>
                <Section>
                    <SectionHeader
                        title={t("Product Create Form")}
                        content={
                            <div className="space-y-3 break-words leading-7">
                                <p>
                                    Create new product to sell to a cheaf price. to make more profit, define your{" "}
                                    <strong>{t("Bying Price")}</strong> and <strong>{t("Selling Price")}</strong>. Keep it mind that,{" "}
                                    <b>{t("Super Admin")}</b> takes <strong>{shop?.system_get_comission ?? "N/A"}%</strong> of
                                    comission from your profit.
                                </p>
                                {requiresShop && (
                                    <div className="block w-full rounded bg-red-200 p-3 text-sm leading-6 text-red-900 shadow-lg">
                                        {t("Please complete your shop profile before creating products.")}
                                    </div>
                                )}
                                {!requiresShop && !ableToCreate && (
                                    <div className="block w-full rounded bg-red-200 p-3 text-sm leading-6 text-red-900 shadow-lg">
                                        {t("You have reached your maximum product upload limit (")}{shop?.max_product_upload ?? 0}{t("). Please contact support to increase your limit.")}
                                    </div>
                                )}
                            </div>
                        }
                    />
                </Section>

                <form onSubmit={submit}>
                    <div className="flex flex-col gap-4 xl:flex-row">
                        <Section className="xl:flex-1">
                            <SectionHeader title={t("Product Basic Info")} content="" />
                            <SectionInner>
                                <InputField
                                    error={form.errors.name}
                                    labelWidth="350px"
                                    label={t("Products Name")}
                                    name="name"
                                    inputClass="w-full"
                                    value={form.data.name}
                                    onChange={(e) => form.setData("name", e.target.value)}
                                />
                                <InputFile
                                    labelWidth="250px"
                                    error="title"
                                    label={t("Products title")}
                                    name="title"
                                    errors={form.errors}
                                >
                                    <textarea
                                        rows="3"
                                        className="w-full rounded"
                                        value={form.data.title}
                                        onChange={(e) => form.setData("title", e.target.value)}
                                    ></textarea>
                                </InputFile>

                                <Hr />
                                <InputFile
                                    labelWidth="250px"
                                    label={t("Products Category")}
                                    error="category_id"
                                    errors={form.errors}
                                >
                                    <div className="text-xs">{t("Category :")}{" "}
                                        <strong>{t("N/A")}</strong>{t(". Change to another")}</div>
                                    <CategorySelect
                                        categories={categories}
                                        value={form.data.category_id ?? ""}
                                        onChange={(categoryId) =>
                                            form.setData("category_id", categoryId)
                                        }
                                        placeholder={t("-- Select Category --")}
                                        noneLabel={t("-- Select Category --")}
                                        noResultsLabel={t("No category found.")}
                                    />
                                </InputFile>
                                <Hr />
                            </SectionInner>
                        </Section>

                        <Section className="xl:w-[324px] xl:flex-none">
                            <SectionHeader title={t("Product Price")} content="" />
                            <SectionInner>
                                <div>
                                    <InputField
                                        className="mx-1"
                                        labelWidth="100px"
                                        label={t("Product Buying Price")}
                                        name="buying_price"
                                        error={form.errors.buying_price}
                                        value={form.data.buying_price}
                                        onChange={(e) => form.setData("buying_price", e.target.value)}
                                    />
                                    <InputField
                                        className="mx-1"
                                        labelWidth="100px"
                                        label={t("Product Sell Price")}
                                        name="price"
                                        error={form.errors.price}
                                        value={form.data.price}
                                        onChange={(e) => form.setData("price", e.target.value)}
                                    />
                                    <InputField
                                        className="mx-1"
                                        labelWidth="100px"
                                        type="number"
                                        label={t("Product Unite")}
                                        name="unit"
                                        error={form.errors.unit}
                                        value={form.data.unit}
                                        onChange={(e) => form.setData("unit", e.target.value)}
                                    />
                                </div>
                                <Hr />
                                <div>
                                    <InputFile label={t("Wish to sell with Discount")} name="offer_type" error="offer_type">
                                        <input
                                            type="checkbox"
                                            checked={!!form.data.offer_type}
                                            onChange={(e) => form.setData("offer_type", e.target.checked)}
                                            style={{ width: 25, height: 25 }}
                                        />
                                    </InputFile>
                                    {form.data.offer_type && (
                                        <InputField
                                            className=""
                                            labelWidth="250px"
                                            label={t("Product Discount Price")}
                                            name="discount"
                                            error={form.errors.discount}
                                            value={form.data.discount}
                                            onChange={(e) => form.setData("discount", e.target.value)}
                                        />
                                    )}
                                </div>
                                <Hr />
                                <div>
                                    <InputFile label={t("Set to Recomended Products")} name="display_at_home" error="display_at_home">
                                        <input
                                            type="checkbox"
                                            checked={!!form.data.display_at_home}
                                            onChange={(e) => form.setData("display_at_home", e.target.checked)}
                                            style={{ width: 25, height: 25 }}
                                        />
                                    </InputFile>
                                </div>
                            </SectionInner>
                        </Section>
                    </div>

                    <div>
                        <Section>
                            <SectionHeader
                                title={t("Product Delevery")}
                                content={t("Define your product delevery option and charge from here.")}
                            />
                            <SectionInner>
                                <div className="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] xl:items-start">
                                    <div className="min-w-0">
                                        <InputFile error="cod" label={t("Available Cash-On-Delevery")} className="lg:flex" name="cod">
                                            <input
                                                type="checkbox"
                                                checked={!!form.data.cod}
                                                onChange={(e) => form.setData("cod", e.target.checked)}
                                                style={{ width: 25, height: 25 }}
                                            />
                                        </InputFile>
                                        <Hr />
                                        <InputFile error="courier" label={t("Available Couried Delivery")} className="lg:flex" name="courier">
                                            <input
                                                type="checkbox"
                                                checked={!!form.data.courier}
                                                onChange={(e) => form.setData("courier", e.target.checked)}
                                                style={{ width: 25, height: 25 }}
                                            />
                                        </InputFile>
                                        <Hr />
                                        <InputFile error="hand" label={t("Available Hand-To-Hand Delevery")} className="lg:flex" name="hand">
                                            <input
                                                type="checkbox"
                                                checked={!!form.data.hand}
                                                onChange={(e) => form.setData("hand", e.target.checked)}
                                                style={{ width: 25, height: 25 }}
                                            />
                                        </InputFile>
                                    </div>
                                    <div className="min-w-0">
                                        <InputField
                                            label={t("Delevery Amount Inside Dhaka")}
                                            name="shipping_in_dhaka"
                                            className="lg:flex"
                                            labelWidth="250px"
                                            error={form.errors.shipping_in_dhaka}
                                            value={form.data.shipping_in_dhaka}
                                            onChange={(e) => form.setData("shipping_in_dhaka", e.target.value)}
                                        />
                                        <Hr />
                                        <InputField
                                            label={t("Normal Delevery Amount")}
                                            className="lg:flex"
                                            name="shipping_out_dhaka"
                                            labelWidth="250px"
                                            error={form.errors.shipping_out_dhaka}
                                            value={form.data.shipping_out_dhaka}
                                            onChange={(e) => form.setData("shipping_out_dhaka", e.target.value)}
                                        />
                                        <Hr />
                                        <InputFile label={t("Shipping Note")} error="shipping_note" name="shipping_note">
                                            <textarea
                                                rows="3"
                                                className="w-full rounded"
                                                placeholder={t("write your shipping note ...")}
                                                value={form.data.shipping_note}
                                                onChange={(e) => form.setData("shipping_note", e.target.value)}
                                            ></textarea>
                                        </InputFile>
                                    </div>
                                </div>
                            </SectionInner>
                        </Section>

                        <Section>
                            <SectionHeader
                                title={t("SEO")}
                                content={t("Setup your product seo from here.")}
                            />
                            <SectionInner>
                                <InputField
                                    error={form.errors.meta_keyword}
                                    label={t("Meta Keyword")}
                                    name="meta_keyword"
                                    className="lg:flex"
                                    inputClass="w-full"
                                    value={form.data.meta_keyword}
                                    onChange={(e) => form.setData("meta_keyword", e.target.value)}
                                />
                                <InputField
                                    error={form.errors.meta_title}
                                    label={t("Meta Title")}
                                    name="meta_title"
                                    className="lg:flex"
                                    inputClass="w-full"
                                    value={form.data.meta_title}
                                    onChange={(e) => form.setData("meta_title", e.target.value)}
                                />
                                <InputField
                                    error={form.errors.meta_tags}
                                    label={t("Meta Tags")}
                                    name="meta_tags"
                                    className="lg:flex"
                                    inputClass="w-full"
                                    value={form.data.meta_tags}
                                    onChange={(e) => form.setData("meta_tags", e.target.value)}
                                />
                                <InputFile label={t("Meta Description")} name="meta_description" error="meta_description" errors={form.errors}>
                                    <textarea
                                        className="w-full p-2 rounded-md shadow"
                                        rows="4"
                                        placeholder={t("Meta Description ....")}
                                        value={form.data.meta_description}
                                        onChange={(e) => form.setData("meta_description", e.target.value)}
                                    ></textarea>
                                </InputFile>

                                <InputFile label={t("Meta Thumbnail")} name="meta_thumbnail" error="meta_thumbnail" errors={form.errors}>
                                    <div>
                                        {metaThumbPreview ? (
                                            <img src={metaThumbPreview} width="100px" height="200px" alt="" />
                                        ) : null}
                                    </div>
                                    <div className="relative">
                                        <p>{t("100 x 200 meta thumbnail")}</p>
                                        <input
                                            type="file"
                                            id="newseothumb"
                                            className="absolute hidden"
                                            onChange={(e) =>
                                                form.setData("meta_thumbnail", e.target.files?.[0] ?? null)
                                            }
                                        />
                                        <label htmlFor="newseothumb">
                                            <i className="px-2 fas fa-upload"></i>
                                        </label>
                                    </div>
                                </InputFile>
                            </SectionInner>
                        </Section>

                        <Section>
                            <SectionHeader
                                title={t("Image Attributes")}
                                content={t("Give your products attributes, product different types, different product color package and quantity.")}
                            />
                            <SectionInner>
                                <ProductAttributesInput
                                    attributes={form.data.attributes}
                                    onChange={(attributes) => form.setData("attributes", attributes)}
                                />
                            </SectionInner>
                        </Section>

                        <Section>
                            <div className="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                                <div className="flex-1">
                                    <SectionHeader
                                        title={t("Image Thumbnail")}
                                        content={
                                            <div>
                                                {t("Provide a mendatory thumbnail image for your products. This image consider for the thumbnail for social media platform.")}
                                                <div className="relative mt-3">
                                                    <p className="mb-2 text-xs">{t("600 x 600 image thumbnail")}</p>
                                                    <input
                                                        type="file"
                                                        className="absolute hidden p-1 border"
                                                        id="prod_thumbnail"
                                                        onChange={(e) => form.setData("thumb", e.target.files?.[0] ?? null)}
                                                    />
                                                    <label
                                                        htmlFor="prod_thumbnail"
                                                        className="inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded border"
                                                    >
                                                        <i className="fas fa-upload"></i>
                                                    </label>
                                                </div>
                                            </div>
                                        }
                                    />
                                </div>

                                <SectionInner className="w-full xl:w-auto">
                                    {thumbPreview ? (
                                        <img src={thumbPreview} className="h-auto max-w-[140px] rounded border" alt="" />
                                    ) : (
                                        <div className="flex h-[140px] w-full items-center justify-center rounded border border-dashed border-slate-300 text-sm text-slate-400 xl:w-[140px]">
                                            {t("No image")}
                                        </div>
                                    )}
                                </SectionInner>
                            </div>
                        </Section>

                        <Section>
                            <SectionHeader
                                title={t("Product Video")}
                                content={t("Add an optional YouTube video URL for the details page.")}
                            />
                            <SectionInner>
                                <InputField
                                    label={t("YouTube URL")}
                                    name="video"
                                    value={form.data.video}
                                    onChange={(e) => form.setData("video", e.target.value)}
                                    error={form.errors.video}
                                />
                            </SectionInner>
                        </Section>

                        <Section>
                            <SectionHeader
                                title={t("Other Image")}
                                content={t("Other product image that showcase your product. other image mainly display at product details page.")}
                            />
                            <SectionInner>
                                <div
                                    style={{
                                        display: "grid",
                                        gridTemplateColumns: "repeat(auto-fit,50px)",
                                        gridGap: "10px",
                                    }}
                                >
                                    {newImagePreviews.map((src, index) => (
                                        <div key={`${src}-${index}`} className="p-2 border rounded">
                                            <img src={src} width="50px" height="50px" alt="" />
                                        </div>
                                    ))}
                                </div>

                                <div className="relative flex flex-wrap items-center gap-3 mt-3">
                                    <input
                                        type="file"
                                        id="multi_prod_img"
                                        className="absolute hidden"
                                        multiple
                                        accept="image/*"
                                        onChange={handleOtherImagesChange}
                                    />
                                    <label
                                        htmlFor="multi_prod_img"
                                        className="inline-flex items-center justify-center border rounded cursor-pointer w-9 h-9"
                                    >
                                        <i className="fas fa-upload"></i>
                                    </label>
                                    <div className="text-xs leading-5">
                                        {t("Please choose all image at once, if you plan to upload multiple image.")}
                                    </div>
                                    {form.errors.newImage ? (
                                        <div className="text-xs text-red-500">
                                            {form.errors.newImage}
                                        </div>
                                    ) : null}
                                </div>
                            </SectionInner>
                        </Section>

                        <Section>
                            <SectionHeader
                                title={t("Description")}
                                content={t("Descrive your product as you need.")}
                            />
                            <SectionInner>
                                <InputFile label={t("Description")} labelWidth="250px" error="description" errors={form.errors}>
                                    <main className="min-w-0">
                                        {trixReady ? (
                                            <div className="overflow-x-auto">
                                                <trix-toolbar id={`my_toolbar_${inputId}`}></trix-toolbar>
                                            </div>
                                        ) : null}
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
                                                className="w-full border-gray-300 rounded"
                                                rows="10"
                                                value={form.data.description}
                                                onChange={(e) => form.setData("description", e.target.value)}
                                            />
                                        )}
                                    </main>
                                </InputFile>
                            </SectionInner>
                        </Section>

                        <div className="pb-4">
                            <PrimaryButton
                                type="submit"
                                className="w-full justify-center sm:w-auto"
                                disabled={form.processing || requiresShop || !ableToCreate}
                            >
                                {t("create")}
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </Container>
        </AppLayout>
    );
}
