import Container from "../../components/dashboard/Container";
import NavLink from "../../components/NavLink";
import SectionSection from "../../components/dashboard/section/Section";
import UserLayout from "../../Layouts/User/App";

export default function Page({ slug = "", page = null, otherPages = [] }) {
    return (
        <UserLayout title={page?.name ?? "Page"}>
            {page ? (
                <Container>
                    <div className="md:flex">
                        <div className="hidden p-2 md:block" style={{ maxWidth: "200px" }}>
                            {otherPages.map((item) => (
                                <NavLink
                                    key={item.id}
                                    href={route("web.pages", { slug: item.slug })}
                                    active={slug === item.slug}
                                    className="w-full py-2 mb-1 border-b"
                                >
                                    <i className="pr-2 text-indigo-900 fas fa-angle-right"></i>
                                    {String(item.name ?? "").charAt(0).toUpperCase() +
                                        String(item.name ?? "").slice(1)}
                                </NavLink>
                            ))}
                        </div>

                        <SectionSection>
                            <div>
                                <div className="p-3 text-lg font-bold bg-gray-50">
                                    {String(page.name ?? "").toUpperCase()}
                                </div>
                                <div
                                    dangerouslySetInnerHTML={{
                                        __html: page.content ?? "",
                                    }}
                                />
                            </div>
                        </SectionSection>
                    </div>
                </Container>
            ) : (
                <p className="w-full py-2 bg-gray-50">Not Found !</p>
            )}
        </UserLayout>
    );
}
