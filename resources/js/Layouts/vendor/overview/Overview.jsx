import Div from "../../../components/dashboard/overview/Div";
import Section from "../../../components/dashboard/overview/Section";

export default function Overview({ products, sales }) {
    return (
        <>
            <p className="mb-2 text-xs">Overall Details</p>
            <Section>
                <Div title="Products" content={<div>{products ?? "0"}</div>} />
                <Div title="Sales" content={<div>Tk {sales ?? "0"}</div>} />
            </Section>
            <hr className="my-2" />
        </>
    );
}
