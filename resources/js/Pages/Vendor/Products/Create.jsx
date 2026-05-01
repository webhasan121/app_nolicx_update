import { Head, useForm } from "@inertiajs/react";
import { useEffect, useId, useMemo, useRef, useState } from "react";
import AppLayout from "../../../Layouts/App";
import Hr from "../../../components/Hr";
import InputField from "../../../components/InputField";
import InputFile from "../../../components/InputFile";
import PrimaryButton from "../../../components/PrimaryButton";
import TextInput from "../../../components/TextInput";
import Container from "../../../components/dashboard/Container";
import PageHeader from "../../../components/dashboard/PageHeader";
import Section from "../../../components/dashboard/section/Section";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";

export default function Create({ categories = [], shop, ableToCreate = true }) {
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
        video: null,
        newImage: [],
        attr_name: "",
        attr_value: "",
    });

    const [thumbPreview, setThumbPreview] = useState(null);
    const [videoPreview, setVideoPreview] = useState(null);
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
        if (!(form.data.video instanceof File)) {
            setVideoPreview(null);
            return undefined;
        }
        const url = URL.createObjectURL(form.data.video);
        setVideoPreview(url);
        return () => URL.revokeObjectURL(url);
    }, [form.data.video]);

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

    const submit = (e) => {
        console.log("form", form);

        e.preventDefault();
        form.post(route("vendor.products.store"), {
            forceFormData: true,
        });
    };

    const categoryOptions = useMemo(() => renderCategoryOptions(categories), [categories]);

    return (
        <AppLayout title="Add Products" header={<PageHeader>Add Products</PageHeader>}>
            <Head title="Add Products" />

            <Container>
                <Section>
                    <SectionHeader
                        title="Product Create Form"
                        content={
                            <>
                                Create new product to sell to a cheaf price. to make more profit, define your{" "}
                                <strong>Bying Price</strong> and <strong>Selling Price</strong>. Keep it mind that,{" "}
                                <b>Super Admin</b> takes <strong> {shop?.system_get_comission ?? "N/A"}% </strong> of
                                comission from your profit.
                                <br />
                                {!ableToCreate && (
                                    <span className="p-3 shadow-lg rounded bg-red-200 text-red-900">
                                        You have reached your maximum product upload limit ({shop?.max_product_upload ?? 0}
                                        ). Please contact support to increase your limit.
                                    </span>
                                )}
                            </>
                        }
                    />
                </Section>

                <form onSubmit={submit}>
                    <div className="md:flex justify-between">
                        <Section>
                            <SectionHeader
                                title="Basic Information"
                                content="Provide your products related basic infromation."
                            />
                            <SectionInner>
                                <InputField
                                    inputClass="w-full"
                                    labelWidth="250px"
                                    label="Product Name"
                                    name="name"
                                    error={form.errors.name}
                                    value={form.data.name}
                                    onChange={(e) => form.setData("name", e.target.value)}
                                />
                                <InputField
                                    inputClass="w-full"
                                    label="Product Title"
                                    name="title"
                                    error={form.errors.title}
                                    value={form.data.title}
                                    onChange={(e) => form.setData("title", e.target.value)}
                                />

                                <InputFile
                                    label="Chose Category"
                                    name="category_id"
                                    error="category_id"
                                    errors={form.errors}
                                    labelWidth={''}
                                >
                                    <select
                                        className="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        value={form.data.category_id}
                                        onChange={(e) => form.setData("category_id", e.target.value)}
                                    >
                                        <option value=""> -- Chose an category -- </option>
                                        {categoryOptions}
                                    </select>
                                </InputFile>
                            </SectionInner>
                        </Section>

                        <Section>
                            <SectionHeader title="Product Price" content="" />
                            <SectionInner>
                                <div>
                                    <InputField
                                        className="mx-1"
                                        labelWidth="100px"
                                        label="Product Buying Price"
                                        name="buying_price"
                                        error={form.errors.buying_price}
                                        value={form.data.buying_price}
                                        onChange={(e) => form.setData("buying_price", e.target.value)}
                                    />
                                    <InputField
                                        className="mx-1"
                                        labelWidth="100px"
                                        label="Product Sell Price"
                                        name="price"
                                        error={form.errors.price}
                                        value={form.data.price}
                                        onChange={(e) => form.setData("price", e.target.value)}
                                    />
                                    <InputField
                                        className="mx-1"
                                        labelWidth="100px"
                                        type="number"
                                        label="Product Unit"
                                        name="unit"
                                        error={form.errors.unit}
                                        value={form.data.unit}
                                        onChange={(e) => form.setData("unit", e.target.value)}
                                    />
                                </div>
                                <Hr />
                                <div>
                                    <InputFile label="Wish to sell with Discount" name="offer_type" error="offer_type">
                                        <input
                                            type="checkbox"
                                            checked={!!form.data.offer_type}
                                            onChange={(e) => form.setData("offer_type", e.target.checked)}
                                            style={{ width: 25, height: 25 }}
                                        />
                                    </InputFile>
                                    {form.data.offer_type && (
                                        <InputField
                                            className="md:flex"
                                            labelWidth="250px"
                                            label="Product Discount Price"
                                            name="discount"
                                            error={form.errors.discount}
                                            value={form.data.discount}
                                            onChange={(e) => form.setData("discount", e.target.value)}
                                        />
                                    )}
                                </div>
                                <Hr />
                                <div>
                                    <InputFile label="Set to Recomended Products" name="display_at_home" error="display_at_home">
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
                                title="Product Delevery"
                                content="Define your product delevery option and charge from here."
                            />
                            <SectionInner>
                                <div className="md:flex justify-between">
                                    <div>
                                        <InputFile error="cod" label="Available Cash-On-Delevery" className="lg:flex" name="cod">
                                            <input
                                                type="checkbox"
                                                checked={!!form.data.cod}
                                                onChange={(e) => form.setData("cod", e.target.checked)}
                                                style={{ width: 25, height: 25 }}
                                            />
                                        </InputFile>
                                        <Hr />
                                        <InputFile error="courier" label="Available Couried Delivery" className="lg:flex" name="courier">
                                            <input
                                                type="checkbox"
                                                checked={!!form.data.courier}
                                                onChange={(e) => form.setData("courier", e.target.checked)}
                                                style={{ width: 25, height: 25 }}
                                            />
                                        </InputFile>
                                        <Hr />
                                        <InputFile error="hand" label="Available Hand-To-Hand Delevery" className="lg:flex" name="hand">
                                            <input
                                                type="checkbox"
                                                checked={!!form.data.hand}
                                                onChange={(e) => form.setData("hand", e.target.checked)}
                                                style={{ width: 25, height: 25 }}
                                            />
                                        </InputFile>
                                    </div>
                                    <div>
                                        <InputField
                                            label="Delevery Amount Inside Dhaka"
                                            name="shipping_in_dhaka"
                                            className="lg:flex"
                                            labelWidth="250px"
                                            error={form.errors.shipping_in_dhaka}
                                            value={form.data.shipping_in_dhaka}
                                            onChange={(e) => form.setData("shipping_in_dhaka", e.target.value)}
                                        />
                                        <Hr />
                                        <InputField
                                            label="Normal Delevery Amount"
                                            className="lg:flex"
                                            name="shipping_out_dhaka"
                                            labelWidth="250px"
                                            error={form.errors.shipping_out_dhaka}
                                            value={form.data.shipping_out_dhaka}
                                            onChange={(e) => form.setData("shipping_out_dhaka", e.target.value)}
                                        />
                                        <Hr />
                                        <InputFile label="Shipping Note" error="shipping_note" name="shipping_note">
                                            <textarea
                                                rows="3"
                                                className="w-full rounded"
                                                placeholder="write your shipping note ... "
                                                value={form.data.shipping_note}
                                                onChange={(e) => form.setData("shipping_note", e.target.value)}
                                            ></textarea>
                                        </InputFile>
                                    </div>
                                </div>
                            </SectionInner>
                        </Section>

                        <Section>
                            <SectionInner>
                                <InputField
                                    error={form.errors.meta_keyword}
                                    label="Meta Keyword"
                                    name="meta_keyword"
                                    className="lg:flex"
                                    inputClass="w-full"
                                    value={form.data.meta_keyword}
                                    onChange={(e) => form.setData("meta_keyword", e.target.value)}
                                />
                                <InputField
                                    error={form.errors.meta_title}
                                    label="Meta Title"
                                    name="meta_title"
                                    className="lg:flex"
                                    inputClass="w-full"
                                    value={form.data.meta_title}
                                    onChange={(e) => form.setData("meta_title", e.target.value)}
                                />
                                <InputField
                                    error={form.errors.meta_tags}
                                    label="Meta Tags"
                                    name="meta_tags"
                                    className="lg:flex"
                                    inputClass="w-full"
                                    value={form.data.meta_tags}
                                    onChange={(e) => form.setData("meta_tags", e.target.value)}
                                />
                                <InputFile label="Meta Description" name="meta_description" error="meta_description" errors={form.errors}>
                                    <textarea
                                        className="rounded-md p-2 shadow w-full"
                                        rows="4"
                                        placeholder="Meta Description ...."
                                        value={form.data.meta_description}
                                        onChange={(e) => form.setData("meta_description", e.target.value)}
                                    ></textarea>
                                </InputFile>

                                <InputFile label="Meta Thumbnail" name="meta_thumbnail" error="meta_thumbnail" errors={form.errors}>
                                    <div>
                                        {metaThumbPreview ? (
                                            <img src={metaThumbPreview} width="100px" height="200px" alt="" />
                                        ) : null}
                                    </div>
                                    <div className="relative">
                                        <p>100 x 200 meta thumbnail</p>
                                        <input
                                            type="file"
                                            id="newseothumb"
                                            className="absolute hidden"
                                            onChange={(e) =>
                                                form.setData("meta_thumbnail", e.target.files?.[0] ?? null)
                                            }
                                        />
                                        <label htmlFor="newseothumb">
                                            <i className="fas fa-upload px-2"></i>
                                        </label>
                                    </div>
                                </InputFile>
                            </SectionInner>
                        </Section>

                        <Section>
                            <SectionHeader
                                title="Products Attributes"
                                content="Give your products attributes, product different types, different product color package and quantity."
                            />
                            <SectionInner>
                                <div className="md:flex">
                                    <TextInput
                                        value={form.data.attr_name}
                                        onChange={(e) => form.setData("attr_name", e.target.value)}
                                        placeholder="Name"
                                    />
                                    <TextInput
                                        value={form.data.attr_value}
                                        onChange={(e) => form.setData("attr_value", e.target.value)}
                                        placeholder="Value"
                                    />
                                </div>
                            </SectionInner>
                        </Section>

                        <Section>
                            <SectionHeader
                                title="Image Thumbnail"
                                content="Provide a mendatory thumbnail image for your products. This image consider for the thumbnail for social media platform."
                            />
                            <SectionInner>
                                <InputFile label="Thumbnail" className="md:flex" labelWidth="250px" error="thumb" errors={form.errors}>
                                    {thumbPreview ? (
                                        <img src={thumbPreview} width="300px" height="300px" alt="" />
                                    ) : null}
                                    <div className="relative">
                                        <input
                                            type="file"
                                            className="absolute hidden"
                                            id="prod_thumbnail"
                                            onChange={(e) => form.setData("thumb", e.target.files?.[0] ?? null)}
                                        />
                                        <label htmlFor="prod_thumbnail" className="p-2 rounded border">
                                            <i className="fas fa-upload"></i>
                                        </label>
                                    </div>
                                </InputFile>
                            </SectionInner>
                        </Section>

                        <Section>
                            <SectionHeader
                                title="Product Video"
                                content="Upload an optional product video for the details page."
                            />
                            <SectionInner>
                                <InputFile label="Video" className="md:flex" labelWidth="250px" error="video" errors={form.errors}>
                                    {videoPreview ? (
                                        <video src={videoPreview} controls className="mb-3 max-h-64 w-full rounded border" />
                                    ) : null}
                                    <div className="relative">
                                        <input
                                            type="file"
                                            className="absolute hidden"
                                            id="product_video"
                                            accept="video/mp4,video/quicktime,video/x-msvideo,video/webm,video/x-matroska"
                                            onChange={(e) => form.setData("video", e.target.files?.[0] ?? null)}
                                        />
                                        <label htmlFor="product_video" className="p-2 rounded border">
                                            <i className="fas fa-upload"></i>
                                        </label>
                                        <p className="mt-2 text-xs">Allowed: mp4, mov, avi, webm, mkv. Max 50MB.</p>
                                    </div>
                                </InputFile>
                            </SectionInner>
                        </Section>

                        <Section>
                            <SectionHeader
                                title="Other Image"
                                content="Other product image that showcase your product. other image mainly display at product details page."
                            />
                            <SectionInner>
                                <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit,50px)", gridGap: "10px" }}>
                                    {newImagePreviews.map((src, index) => (
                                        <div key={`${src}-${index}`} className="p-2 border rounded">
                                            <img src={src} width="50px" height="50px" alt="" />
                                        </div>
                                    ))}
                                </div>

                                <div className="relative">
                                    <input
                                        type="file"
                                        id="multi_prod_img"
                                        className="absolute hidden"
                                        multiple
                                        accept="image/*"
                                        onChange={(e) =>
                                            form.setData("newImage", Array.from(e.target.files ?? []))
                                        }
                                    />
                                    <label htmlFor="multi_prod_img" className="p-2 border rounded">
                                        <i className="fas fa-upload"></i>
                                    </label>
                                </div>
                                <div className="text-xs">
                                    Please choose all image at once, if you plan to upload multiple image.
                                </div>
                            </SectionInner>
                        </Section>

                        <Section>
                            <SectionHeader
                                title="Description"
                                content="Descrive your product as you need."
                            />
                            <SectionInner>
                                <div className="flex flex-wrap items-center p-3 border-b bg-gray-50 gap-2"></div>
                                <InputFile label="Description" className="md:flex" labelWidth="250px" error="description" errors={form.errors}>
                                    <hr />
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
                                                className="w-full rounded-md shadow-sm border-gray-300"
                                                rows="10"
                                                value={form.data.description}
                                                onChange={(e) => form.setData("description", e.target.value)}
                                            />
                                        )}
                                    </main>
                                    <br />
                                    <PrimaryButton type="submit" className="block" disabled={form.processing}>
                                        create
                                    </PrimaryButton>
                                </InputFile>
                            </SectionInner>
                        </Section>
                    </div>
                </form>
            </Container>
        </AppLayout>
    );
}

function renderCategoryOptions(categories = [], depth = 0) {
    return categories.flatMap((category) => {
        const label = `${depth === 0 ? "" : "-".repeat(depth * 2) + " "}${category.name}`;
        const options = [
            <option key={category.id} value={category.id}>
                {label}
            </option>,
        ];

        if (Array.isArray(category.children) && category.children.length > 0) {
            category.children.forEach((child) => {
                options.push(
                    <option key={child.id} value={child.id}>
                        {`${"-".repeat((depth + 1) * 2)} ${child.name}`}
                    </option>
                );

                if (Array.isArray(child.children) && child.children.length > 0) {
                    child.children.forEach((grandChild) => {
                        options.push(
                            <option key={grandChild.id} value={grandChild.id}>
                                {`${"-".repeat((depth + 2) * 2)} ${grandChild.name}`}
                            </option>
                        );
                    });
                }
            });
        }

        return options;
    });
}
