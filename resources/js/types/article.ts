export type ArticleAuthor = {
    id: number;
    name: string;
};

export type Taxonomy = {
    id: number;
    name: string;
    slug: string;
};

export type Article = {
    id: number;
    title: string;
    slug: string;
    image_url: string;
    image_alt: string;
    summary: string;
    body: string;
    rating: number;
    affiliate_url: string | null;
    published_at: string | null;
    created_at: string | null;
    updated_at: string | null;
    author: ArticleAuthor;
    category: Taxonomy;
    tags: Taxonomy[];
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        from: number | null;
        last_page: number;
        links: PaginationLink[];
        path: string;
        per_page: number;
        to: number | null;
        total: number;
    };
};

export type CategoryOption = {
    id: number;
    name: string;
};
