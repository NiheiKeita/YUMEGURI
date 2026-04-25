export type SentoStatus = 'open' | 'closed_temp' | 'closed_perm'

export type SentoSummary = {
    id: number
    name: string
    name_kana?: string | null
    prefecture: string
    city?: string | null
    address: string
    lat: number | null
    lng: number | null
    phone?: string | null
    hours?: string | null
    closed_days?: string | null
    price?: number | null
    source_url?: string | null
    nearest_station?: string | null
    walk_minutes?: number | null
    has_shampoo: boolean
    has_soap: boolean
    status: SentoStatus
    avg_rating?: number | null
    review_count?: number | null
    distance_km?: number | null
}

export type PhotoCategory = 'exterior' | 'interior' | 'locker' | 'other'

export type SentoPhoto = {
    id: number
    sento_id: number
    review_id: number | null
    url: string | null
    category: PhotoCategory
    caption: string | null
}

export type SentoReview = {
    id: number
    sento_id: number
    user?: { id: number; name: string }
    visited_at: string
    rating: number
    body: string | null
    has_sauna: boolean
    sauna_temp: number | null
    has_mizuburo: boolean
    mizuburo_temp: number | null
    bath_types: string[]
    want_revisit: boolean
    crowding: number | null
    best_time: string | null
    photos?: SentoPhoto[]
    sento?: SentoSummary
}

export type SentoDetail = SentoSummary & {
    reviews?: SentoReview[]
    photos?: SentoPhoto[]
}

export type ProposalStatus = 'pending' | 'approved' | 'rejected'

export type SentoEditProposal = {
    id: number
    sento_id: number
    sento?: { id: number; name: string }
    proposer?: { id: number; name: string }
    changes: Record<string, unknown>
    reason: string | null
    status: ProposalStatus
    reviewed_at: string | null
    created_at: string
}

export type Pin = {
    id: number
    name: string
    lat: number
    lng: number
    visited?: boolean
    visited_at?: string
    rating?: number
}

export type Paginated<T> = {
    data: T[]
    links: { first: string; last: string; prev: string | null; next: string | null }
    meta: {
        current_page: number
        from: number | null
        last_page: number
        per_page: number
        to: number | null
        total: number
    }
}
